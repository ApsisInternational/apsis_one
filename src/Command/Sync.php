<?php

namespace Apsis\One\Command;

use Apsis\One\Model\EntityInterface as EI;
use Apsis\One\Model\Event;
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

                $profilesToSync = $this->entityHelper
                    ->getProfileRepository()
                    ->findBySyncStatusForGivenShop([EI::SS_PENDING], [$shopId]);

                if (! empty($profilesToSync) && is_array($profilesToSync)) {
                    $items = [];
                    foreach ($profilesToSync as $profile) {
                        if (! empty($item = $this->entityHelper->getProfileDataArrForExport($profile, false))) {
                            $items[$profile->getId()] = $item;
                        }
                    }

                    if (! empty($items)) {
                        // @todo Sync
                        //$this->entityHelper->logInfoMsg(var_export($items, true));

                        // Change status to synced
                        $num = $this->entityHelper->updateStatusForEntityByIds(
                            EI::T_PROFILE,
                            EI::SS_SYNCED,
                            array_keys($items)
                        );
                        $message .= sprintf( "\nSynced %d Profiles for Shop ID: %d.", $num, $shopId);
                    }
                }
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
            foreach ($this->shopContext->getAllActiveShopsList() as $shop) {
                $shopId = (int) $shop[EI::C_ID_SHOP];
                $this->loadGenericContext($shopId);

                if (is_string($check = $this->isModuleAndFeatureActiveAndConnected($shopId, self::JOB_TYPE_EVENT))) {
                    $message .= $check;
                    continue;
                }

                $eventsToSync = $this->entityHelper
                    ->getEventRepository()
                    ->findBySyncStatusForGivenShop([EI::SS_PENDING], [$shopId]);

                if (! empty($eventsToSync) && is_array($eventsToSync)) {
                    $groupedEventsByProfile = [];

                    /** @var Event $event */
                    foreach ($eventsToSync as $event) {
                        $eventsDataArr = $this->entityHelper->getEventDataArrForExport($event);
                        if (! empty($eventsDataArr) && is_array($eventsDataArr)) {
                            foreach ($eventsDataArr as $index => $eventArr) {
                                $groupedEventsByProfile[$event->getIdApsisProfile()][$index] = $eventArr;
                            }
                        }
                    }

                    if (! empty($groupedEventsByProfile)) {
                        //$this->entityHelper->logInfoMsg(var_export($groupedEventsByProfile, true));
                        foreach ($groupedEventsByProfile as $profileId => $groupedEvents) {
                            $profile = $this->entityHelper
                                ->getProfileRepository()
                                ->findOneById($profileId);

                            //@todo Sync Profile
                            //@todo Sync Events

                            // Change status to synced
                            $num = $this->entityHelper->updateStatusForEntityByIds(
                                EI::T_EVENT,
                                EI::SS_SYNCED,
                                $this->getEventIdsFromArr($groupedEvents)
                            );
                            $message .= sprintf( "\nSynced %d Events for Shop ID: %d.", $num, $shopId);
                        }
                    }
                }
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
            if (stripos($key , 'p') !== false && stripos($key , 'c') === false) {
                $eventIds[] = (int) str_replace('p', '', $key);
            }
        }
        return $eventIds;
    }
}