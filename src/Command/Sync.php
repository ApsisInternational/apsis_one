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
use Throwable;

class Sync extends AbstractCommand
{
    use LockableTrait;

    /**
     * @var string
     */
    protected $commandName = 'apsis-one:sync';

    /**
     * @var string
     */
    protected $commandDesc = self::COMMAND_DESC_SYNC;

    /**
     * @var string
     */
    protected $commandHelp = 'This commands allows you to ' . self::COMMAND_DESC_SYNC;

    /**
     * @var array
     */
    protected $processorMsg = ['=====================', 'APSIS Sync Operations', '=====================', ''];

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
    protected function processCommand(): int
    {
        try {
            $this->syncProfiles();
            $this->syncEvents();
        } catch (Throwable $e) {
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            $this->output->writeln($e->getMessage());
        }

        $this->release();
        return 0;
    }

    /**
     * Start Profile sync process for each shop
     */
    private function syncProfiles(): void
    {
        $this->entityHelper->logInfoMsg(__METHOD__);
        $message = '';

        try {
            foreach ($this->shopContext->getAllActiveShopsList() as $shop) {
                $shopId = (int) $shop[EI::C_ID_SHOP];

                if (is_string($check = $this->isModuleAndFeatureActiveAndConnected($shopId, self::JOB_TYPE_PROFILE))) {
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
                /** @var Profile $profile */
                foreach ($profilesToSync as $profile) {
                    if (empty($item = $this->entityHelper->getProfileDataArrForExport($profile, false))) {
                        continue;
                    }

                    unset($item[SI::PROFILE_SCHEMA_TYPE_EVENTS]);
                    $items[$profile->getId()] = array_merge(['update_type' => 'profile'], $item);
                }

                if (empty($items)) {
                    continue;
                }

                $ids = array_keys($items);
                $result = $client->justinDsmInsertOrUpdate(
                    $this->installationConfigs[SetupInterface::INSTALLATION_CONFIG_SECTION_DISCRIMINATOR],
                    array_values($items)
                );

                if ($result === false) {
                    continue;
                } elseif (is_string($result)) {
                    $this->entityHelper->updateStatusForEntityByIds(EI::T_PROFILE, EI::SS_FAILED, $ids, $result);
                    continue;
                }

                $num = $this->entityHelper->updateStatusForEntityByIds(EI::T_PROFILE, EI::SS_SYNCED, $ids);
                $message .= sprintf( "\nSynced %d Profiles for Shop ID: %d.", $num, $shopId);
            }
        } catch (Throwable $e) {
            $this->outputRuntimeErrorMsg(self::JOB_TYPE_PROFILE, $e->getMessage());
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            return;
        }

        $this->outputSuccessMsg(self::JOB_TYPE_PROFILE, $message);
    }

    /**
     * Start Event sync process for each shop
     */
    private function syncEvents(): void
    {
        $this->entityHelper->logInfoMsg(__METHOD__);
        $message = '';

        try {
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

                $eventsDiscToVerMapping = $this->moduleHelper->getEventsDiscToVerMapping(
                    $client, $this->installationConfigs[SetupInterface::INSTALLATION_CONFIG_SECTION_DISCRIMINATOR]
                );

                if (empty($eventsDiscToVerMapping)) {
                    continue;
                }

                $groupedEvents = [];

                /** @var Event $event */
                foreach ($eventsCollection as $event) {
                    $eventsDataArr = $this->entityHelper->getEventDataArrForExport($event, HI::ISO_8601);

                    if (empty($eventsDataArr) || ! is_array($eventsDataArr)) {
                        continue;
                    }

                    foreach ($eventsDataArr as $index => $eventArr) {
                        $discriminator = $eventArr[SI::SCHEMA_PROFILE_EVENT_ITEM_DISCRIMINATOR];

                        if (! isset($eventsDiscToVerMapping[$discriminator])) {
                            continue;
                        }

                        $groupedEvents[$event->getIdApsisProfile()][$index] = [
                            'event_time' => $eventArr[SI::SCHEMA_PROFILE_EVENT_ITEM_TIME],
                            'version_id' => $eventsDiscToVerMapping[$discriminator],
                            'data' => $eventArr[SI::SCHEMA_PROFILE_EVENT_ITEM_DATA]
                        ];
                    }
                }

                if (! empty($groupedEvents)) {
                    $message = $this->syncGroupedEventsPerProfile($groupedEvents, $client, $shopId, $message);
                }
            }
        } catch (Throwable $e) {
            $this->outputRuntimeErrorMsg(self::JOB_TYPE_EVENT, $e->getMessage());
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            return;
        }

        $this->outputSuccessMsg(self::JOB_TYPE_EVENT, $message);
    }

    /**
     * @param array $gEvents
     * @param Client $client
     * @param int $shopId
     * @param string $message
     *
     * @return string
     */
    protected function syncGroupedEventsPerProfile(array $gEvents, Client $client, int $shopId, string $message): string
    {
        try {
            foreach ($gEvents as $profileId => $events) {
                if (empty($profileId) || empty($events)) {
                    continue;
                }

                $eventIds = $this->getEventIdsFromArr($events);
                $profile = $this->entityHelper
                    ->getProfileRepository()
                    ->findOneById($profileId);

                if (! $profile instanceof Profile) {
                    continue;
                }

                $this->syncProfileForEvent($client, $profile);
                $status = $client->addEventsToProfile(
                    $this->installationConfigs[SetupInterface::INSTALLATION_CONFIG_KEYSPACE_DISCRIMINATOR],
                    $profile->getIdIntegration(),
                    $this->installationConfigs[SetupInterface::INSTALLATION_CONFIG_SECTION_DISCRIMINATOR],
                    array_values($events)
                );

                if ($status === false) {
                    continue;
                } elseif (is_string($status)) {
                    $this->entityHelper->updateStatusForEntityByIds(EI::T_EVENT, EI::SS_FAILED, $eventIds, $status);
                    continue;
                }

                $num = $this->entityHelper->updateStatusForEntityByIds(EI::T_EVENT, EI::SS_SYNCED, $eventIds);
                $message .= sprintf("\nSynced %d Events for Shop ID: %d.", $num, $shopId);
            }
        } catch (Throwable $e) {
            $this->outputRuntimeErrorMsg(self::JOB_TYPE_EVENT, $e->getMessage());
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
        }

        return $message;
    }

    /**
     * @param Client $client
     * @param Profile $profile
     *
     * @return void
     */
    protected function syncProfileForEvent(Client $client, Profile $profile): void
    {
        try {
            if ($profile->getSyncStatus() === EI::SS_SYNCED) {
                return;
            }

            $secDisc = $this->installationConfigs[SetupInterface::INSTALLATION_CONFIG_SECTION_DISCRIMINATOR];

            $emailAttributeVersionId = $this->moduleHelper->getAttributeVersionId($client, $secDisc, HI::EMAIL_DISC);
            if (empty($emailAttributeVersionId)) {
                return;
            }

            $attributes = [$emailAttributeVersionId => $profile->getEmail()];

            if ($profile->getIdCustomer() && $customer = $this->moduleHelper->getCustomerById($profile->getIdCustomer())) {
                $fNameAttributeVersionId = $this->moduleHelper->getAttributeVersionId($client, $secDisc, HI::F_NAME_DISC);
                if (strlen($customer->firstname) && $fNameAttributeVersionId) {
                    $attributes[$fNameAttributeVersionId] = $customer->firstname;
                }

                $lNameAttributeVersionId = $this->moduleHelper->getAttributeVersionId($client, $secDisc, HI::L_NAME_DISC);
                if (strlen($customer->lastname) && $lNameAttributeVersionId) {
                    $attributes[$lNameAttributeVersionId] = $customer->lastname;
                }
            }

            $client->addAttributesToProfile(
                $this->installationConfigs[SetupInterface::INSTALLATION_CONFIG_KEYSPACE_DISCRIMINATOR],
                $profile->getIdIntegration(),
                $secDisc,
                $attributes
            );

        } catch (Throwable $e) {
            $this->outputRuntimeErrorMsg(self::JOB_TYPE_EVENT, $e->getMessage());
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
        }
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