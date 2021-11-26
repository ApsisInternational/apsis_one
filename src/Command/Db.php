<?php

namespace Apsis\One\Command;

use Apsis\One\Helper\DateHelper;
use Apsis\One\Helper\HelperInterface as HI;
use Apsis\One\Module\SetupInterface;
use DateTime;
use mysqli;
use PDOStatement;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Apsis\One\Model\EntityInterface as EI;
use Db as PsDb;
use Throwable;

class Db extends AbstractCommand
{
    use LockableTrait;

    /**
     * @var string
     */
    protected $commandName = self::COMMAND_NAME_DB;

    /**
     * @var string
     */
    protected $commandDesc = self::COMMAND_DESC_DB;

    /**
     * @var string
     */
    protected $commandHelp = self::COMMAND_HELP_DESC_DB;

    /**
     * @var string
     */
    protected $argumentReqDesc = self::ARG_REQ_DESC_DB;

    /**
     * @var array
     */
    protected $processorMsg = self:: MSG_PROCESSOR_DB;

    /**
     * @var DateHelper
     */
    protected $dateHelper;

    /**
     * @inheritDoc
     */
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->dateHelper = $this->moduleHelper->getService(HI::SERVICE_HELPER_DATE);
    }

    /**
     * {@inheritdoc}
     */
    protected function processCommand(InputInterface $input, OutputInterface $output): int
    {
        try {
            switch ($input->getArgument(self::ARG_REQ_JOB)) {
                case self::JOB_TYPE_SCAN_AC:
                    $this->scanDbForAbandonedCarts($output);
                    break;
                case self::JOB_TYPE_SCAN_SUBS_UPDATE:
                    $this->scanDbForSilentSubscriptionUpdate($output);
                    break;
                case self::JOB_TYPE_SCAN_MISSING_PROFILES:
                    $this->scanDbForSilentMissingProfilesFromSqlImport($output);
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
    private function scanDbForSilentMissingProfilesFromSqlImport(OutputInterface $output): void
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

                $fromTime = $this->getFromDateTimeForGivenShop($shop);
                $toTime = clone $fromTime;
                $fromTime->sub($this->dateHelper->getDateIntervalFromIntervalSpec('PT1440M')); //24Hour

                foreach (SetupInterface::T_PROFILE_MIGRATE_DATA_FROM_TABLES as $table => $sql) {
                    $cond = sprintf(
                        EI::PROFILE_SQL_INSERT_COND,
                        SetupInterface::T_DATE_COLUMN_MAP[$table],
                        $fromTime->format('Y-m-d H:i:s'),
                        $toTime->format('Y-m-d H:i:s'),
                        $shopId
                    );
                    $entity = sprintf("Profiles (%s)", $table);
                    $message .= $this->executeQueryAndGetResultString($sql . $cond, $shopId, $entity);
                }
            }
        } catch (Throwable $e) {
            $this->outputRuntimeErrorMsg($output, self::JOB_TYPE_SCAN_MISSING_PROFILES, $e->getMessage());
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            return;
        }

        $this->outputSuccessMsg($output, self::JOB_TYPE_SCAN_MISSING_PROFILES, $message);
    }

    /**
     * @param OutputInterface $output
     */
    private function scanDbForAbandonedCarts(OutputInterface $output): void
    {
        $this->entityHelper->logInfoMsg(__METHOD__);
        $message = '';

        try {
            foreach ($this->shopContext->getAllActiveShopsList() as $shop) {
                if (is_string($check = $this->isModuleAndFeatureActiveAndConnected((int) $shop[EI::C_ID_SHOP]))) {
                    $message .= $check;
                    continue;
                }

                $fromTime = $this->getFromDateTimeForGivenShop($shop)
                    ->sub($this->dateHelper->getDateIntervalFromIntervalSpec('PT60M'));
                $toTime = clone $fromTime;
                $fromTime->sub($this->dateHelper->getDateIntervalFromIntervalSpec('PT5M'));

                $sql = sprintf(
                    EI::ABANDONED_CART_INSERT_SQL,
                    EI::ABANDONED_CART_INSERT_SQL_ITEMS,
                    (int) $shop[EI::C_ID_SHOP],
                    EI::ABANDONED_CART_INSERT_SQL_ITEMS,
                    $fromTime->format('Y-m-d H:i:s'), // '2021-01-03 15:59:10'
                    $toTime->format('Y-m-d H:i:s') // '2021-12-03 15:59:10'
                );
                $message .= $this->executeQueryAndGetResultString($sql, $shop[EI::C_ID_SHOP], 'Abandoned Carts');
            }
        } catch (Throwable $e) {
            $this->outputRuntimeErrorMsg($output, self::JOB_TYPE_SCAN_AC, $e->getMessage());
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            return;
        }

        $this->outputSuccessMsg($output, self::JOB_TYPE_SCAN_AC, $message);
    }

    /**
     * @param OutputInterface $output
     */
    private function scanDbForSilentSubscriptionUpdate(OutputInterface $output): void
    {
        $this->entityHelper->logInfoMsg(__METHOD__);
        $message = '';

        try {
            foreach ($this->shopContext->getAllActiveShopsList() as $shop) {
                $updated = $eventsCreated = 0;
                $shopId = (int) $shop[EI::C_ID_SHOP];
                if (is_string($check = $this->isModuleAndFeatureActiveAndConnected($shopId))) {
                    $message .= $check;
                    continue;
                }

                $subsNeedUpd = PsDb::getInstance()
                    ->executeS(sprintf(EI::PROFILE_SQL_SUBSCRIBER_SELECT_NEEDING_UPDATE, $shopId));
                if (is_array($subsNeedUpd) && ! empty($subsNeedUpd)) {
                    PsDb::getInstance()->query(sprintf(EI::PROFILE_SQL_SUBSCRIBER_UPDATE_NEEDING_UPDATE, $shopId));
                    $updated += count($subsNeedUpd);
                    $eventsCreated += $this->entityHelper->registerSubsEventsForSubscribers($subsNeedUpd);
                }

                $customerNeedsUpdate = PsDb::getInstance()
                    ->executeS(sprintf(EI::PROFILE_SQL_CUSTOMER_SELECT_NEEDING_UPDATE, $shopId));
                if (is_array($customerNeedsUpdate) && ! empty($customerNeedsUpdate)) {
                    PsDb::getInstance()->query(sprintf(EI::PROFILE_SQL_CUSTOMER_UPDATE_NEEDING_UPDATE, $shopId));
                    $updated += count($customerNeedsUpdate);
                    $eventsCreated += $this->entityHelper->registerSubsEventsForCustomers($customerNeedsUpdate);
                }

                $message .= sprintf(
                    "\nUpdated %d Profiles, inserted %d Events for Shop ID: %d.",
                    $updated,
                    $eventsCreated,
                    $shopId
                );
            }
        } catch (Throwable $e) {
            $this->outputRuntimeErrorMsg($output, self::JOB_TYPE_SCAN_SUBS_UPDATE, $e->getMessage());
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            return;
        }

        $this->outputSuccessMsg($output, self::JOB_TYPE_SCAN_SUBS_UPDATE, $message);
    }

    /**
     * @param array $shopDataArr
     *
     * @return DateTime
     *
     * @throws Throwable
     */
    private function getFromDateTimeForGivenShop(array $shopDataArr): DateTime
    {
        return $this->dateHelper
            ->getDateTimeFromTimeAndTimeZone(
                'now',
                $this->dateHelper->getShopsTimezone($shopDataArr[EI::C_ID_SHOP . '_group'], $shopDataArr[EI::C_ID_SHOP])
            );
    }

    /**
     * @param string $sql
     * @param int $idShop
     * @param string $entity
     *
     * @return string
     */
    private function executeQueryAndGetResultString(string $sql, int $idShop, string $entity): string
    {
        try {
            $result = PsDb::getInstance()->query($sql);
            if (($result instanceof PDOStatement && $num = $result->rowCount()) ||
                ($result instanceof mysqli && $num = $result->affected_rows)
            ) {
                return sprintf( "\nFound %d %s for Shop ID: %d.", $num, $entity, $idShop);
            } else {
                return sprintf( "\nFound 0 %s for Shop ID: %d.", $entity, $idShop);
            }
        } catch (Throwable $e) {
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            return $e->getMessage();
        }
    }
}