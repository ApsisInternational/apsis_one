<?php

namespace Apsis\One\Command;

use Apsis\One\Api\Client;
use Apsis\One\Helper\HelperInterface as HI;
use Apsis\One\Model\EntityInterface as EI;
use Apsis\One\Model\Event;
use Apsis\One\Model\Profile;
use Apsis\One\Api\ClientFactory;
use Apsis\One\Model\SchemaInterface as SI;
use Apsis\One\Module\SetupInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class Sync extends AbstractCommand
{
    use LockableTrait;

    /**
     * @var string
     */
    protected $commandName = self::COMMAND_NAME_SYNC;

    /**
     * @var string
     */
    protected $commandDesc = self::COMMAND_DESC_SYNC;

    /**
     * @var string
     */
    protected $commandHelp = self::COMMAND_HELP_DESC_SYNC;

    /**
     * @var string
     */
    protected $argumentReqDesc = self::ARG_REQ_DESC_SYNC;

    /**
     * @var array
     */
    protected $processorMsg = self:: MSG_PROCESSOR_SYNC;

    /**
     * @var ClientFactory
     */
    protected $clientFactory;

    /**
     * @inheritDoc
     */
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->clientFactory = $this->moduleHelper->getService(HI::SERVICE_MODULE_API_CLIENT_FACTORY);
    }

    /**
     * {@inheritdoc}
     */
    protected function processCommand(InputInterface $input, OutputInterface $output): int
    {
        try {
            switch ($input->getArgument(self::ARG_REQ_JOB)) {
                case self::JOB_TYPE_PROFILE:
                    $this->syncProfiles($output);
                    break;
                case self::JOB_TYPE_EVENT:
                    $this->syncEvents($output);
                    break;
                default:
                    $this->outputInvalidJobErrorMsg($input, $output);
            }

            $this->release();
        } catch (Throwable $e) {
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            $output->writeln($e->getMessage());
        }

        return 0;
    }

    /**
     * @param OutputInterface $output
     */
    private function syncProfiles(OutputInterface $output): void
    {
        $this->entityHelper->logInfoMsg(__METHOD__);
        $message = '';

        try {
            foreach ($this->shopContext->getAllActiveShopsList() as $shop) {
                $shopId = (int) $shop[EI::C_ID_SHOP];

                if (is_string($check = $this->isModuleAndFeatureActiveAndConnected($shopId))) {
                    $message .= $check;
                    continue;
                }

                $client = $this->clientFactory->getApiClient(null, $shopId);
                $profilesToSync = $this->entityHelper
                    ->getProfileRepository()
                    ->findBySyncStatusForGivenShop([EI::SS_PENDING], [$shopId]);
                if (empty($profilesToSync) || ! is_array($profilesToSync) || ! $client instanceof Client) {
                    continue;
                }

                $items = [];
                foreach ($profilesToSync as $profile) {
                    if (! empty($item = $this->entityHelper->getProfileDataArrForExport($profile, false))) {
                        $items[$profile->getId()] = array_merge(
                            [self::KEY_UPDATE_TYPE => self::SYNC_UPDATE_TYPE_PROFILE],
                            $item
                        );
                    }
                }

                if (empty($items)) {
                    continue;
                }

                //$this->entityHelper->logInfoMsg(var_export($items, true));

                $ids = array_keys($items);
                $result = $client->justinDsmInsertOrUpdate(
                    $this->installationConfigs[SetupInterface::INSTALLATION_CONFIG_SECTION_DISCRIMINATOR], $items
                );
                if ($result === false) {
                    $this->entityHelper->logInfoMsg(
                        sprintf("%s. Unable to post Profiles, Shop {%d}. Will try again", __METHOD__, $shopId)
                    );
                    continue;
                } elseif (is_string($result)) {
                    $this->entityHelper->logInfoMsg(
                        sprintf("%s. Unable to post Profiles, Error {%s} Shop {%d}", __METHOD__, $result, $shopId)
                    );
                    $this->entityHelper->updateStatusForEntityByIds(EI::T_PROFILE, EI::SS_FAILED, $ids, $result);
                    continue;
                }

                $num = $this->entityHelper->updateStatusForEntityByIds(EI::T_PROFILE, EI::SS_SYNCED, $ids);
                $message .= sprintf( "\nSynced %d Profiles for Shop ID: %d.", $num, $shopId);
            }
        } catch (Throwable $e) {
            $this->outputRuntimeErrorMsg($output, self::JOB_TYPE_PROFILE, $e->getMessage());
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            return;
        }

        $this->outputSuccessMsg($output, self::JOB_TYPE_PROFILE, $message);
    }

    /**
     * @param OutputInterface $output
     */
    private function syncEvents(OutputInterface $output): void
    {
        $this->entityHelper->logInfoMsg(__METHOD__);
        $message = '';

        try {
            $profiles = [];
            foreach ($this->shopContext->getAllActiveShopsList() as $shop) {
                $shopId = (int) $shop[EI::C_ID_SHOP];
                $this->loadGenericContext($shopId);

                if (is_string($check = $this->isModuleAndFeatureActiveAndConnected($shopId, self::JOB_TYPE_EVENT))) {
                    $message .= $check;
                    continue;
                }

                $client = $this->clientFactory->getApiClient(null, $shopId);
                $eventsCollection = $this->entityHelper
                    ->getEventRepository()
                    ->findBySyncStatusForGivenShop([EI::SS_PENDING], [$shopId]);
                if (empty($eventsCollection) || ! is_array($eventsCollection) || ! $client instanceof Client) {
                    continue;
                }

                $items = [];

                /** @var Event $event */
                foreach ($eventsCollection as $event) {
                    $eventsDataArr = $this->entityHelper->getEventDataArrForExport($event);
                    if (empty($eventsDataArr) || ! is_array($eventsDataArr)) {
                        continue;
                    }

                    foreach ($eventsDataArr as $index => $eventArr) {
                        $profileId = $event->getIdApsisProfile();
                        if (! isset($profiles[$profileId])) {
                            $profiles[$profileId] = $this->entityHelper
                                ->getProfileRepository()
                                ->findOneById($profileId);
                        }

                        if (! $profiles[$profileId] instanceof Profile) {
                            $this->entityHelper->logInfoMsg(
                                sprintf("%s. Profile with id {%d} not found.", __METHOD__, $profileId)
                            );
                            continue;
                        }

                        $items[$index] = [
                            self::KEY_UPDATE_TYPE => self::SYNC_UPDATE_TYPE_EVENT,
                            SI::PROFILE_SCHEMA_TYPE_ENTRY => $profiles[$profileId]->getIdIntegration(),
                            SI::PROFILE_SCHEMA_TYPE_FIELDS => $eventArr
                        ];
                    }
                }

                if (empty($items)) {
                    continue;
                }

                //$this->entityHelper->logInfoMsg(var_export($items, true));

                $ids = $this->getEventIdsFromArr($items);
                $result = $client->justinDsmInsertOrUpdate(
                    $this->installationConfigs[SetupInterface::INSTALLATION_CONFIG_SECTION_DISCRIMINATOR], $items
                );
                if ($result === false) {
                    $this->entityHelper->logInfoMsg(
                        sprintf("%s. Unable to post Events, Shop {%d}. Will try again", __METHOD__, $shopId)
                    );
                    continue;
                } elseif (is_string($result)) {
                    $this->entityHelper->logInfoMsg(
                        sprintf("%s. Unable to post Events, Error {%s} Shop {%d}", __METHOD__, $result, $shopId)
                    );
                    $this->entityHelper->updateStatusForEntityByIds(EI::T_EVENT, EI::SS_FAILED, $ids, $result);
                    continue;
                }

                $num = $this->entityHelper->updateStatusForEntityByIds(EI::T_EVENT, EI::SS_SYNCED, $ids);
                $message .= sprintf("\nSynced %d Events for Shop ID: %d.", $num, $shopId);

            }
        } catch (Throwable $e) {
            $this->outputRuntimeErrorMsg($output, self::JOB_TYPE_EVENT, $e->getMessage());
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            return;
        }

        $this->outputSuccessMsg($output, self::JOB_TYPE_EVENT, $message);
    }

    /**
     * @param array $items
     * @return array
     */
    protected function getEventIdsFromArr(array $items): array
    {
        $eventIds = [];
        foreach ($items as $key => $item) {
            if (is_int($key)) {
                $eventIds[] = $key;
            }
        }
        return $eventIds;
    }
}