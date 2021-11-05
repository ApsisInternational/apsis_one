<?php

namespace Apsis\One\Command;

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
     * {@inheritdoc}
     */
    protected function processCommand(InputInterface $input, OutputInterface $output): ?int
    {
        try {
            switch ($input->getArgument(self::ARG_REQ_JOB)) {
                case self::JOB_TYPE_SCAN_AC:
                    $this->scanDbForAbandonedCarts($input, $output);
                    break;
                case self::JOB_TYPE_SCAN_SUBS_UPDATE:
                    $this->scanDbForSilentSubscriptionUpdate($input, $output);
                    break;
                case self::JOB_TYPE_SCAN_MISSING_PROFILES:
                    $this->scanDbForSilentMissingProfilesFromSqlImport($input, $output);
                    break;
                default:
                    $this->outputErrorMsg($input, $output);
            }

            $this->release();
        } catch (Throwable $e) {
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            $output->writeln($e->getMessage());
        }

        return 0;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function scanDbForSilentMissingProfilesFromSqlImport(InputInterface $input, OutputInterface $output): void
    {
        $this->entityHelper->logInfoMsg(__METHOD__);
        $message = '';

        try {
            foreach ($this->shopContext->getAllActiveShopsList() as $shop) {
                $fromTime = $this->getFromDateTimeForGivenShop($shop);
                $toTime = clone $fromTime;
                $fromTime->sub($this->dateHelper->getDateIntervalFromIntervalSpec('PT1440M'));

                foreach (SetupInterface::T_PROFILE_MIGRATE_DATA_FROM_TABLES as $table => $sql) {
                    $cond = sprintf(
                        EI::PROFILE_SQL_INSERT_COND,
                        SetupInterface::T_DATE_COLUMN_MAP[$table],
                        $fromTime->format('Y-m-d H:i:s'),
                        $toTime->format('Y-m-d H:i:s'),
                        $shop[EI::C_ID_SHOP],
                    );
                    $message .= $this->executeQueryAndGetResultString($sql . $cond, $shop[EI::C_ID_SHOP], "Profiles ($table)");
                }
            }
        } catch (Throwable $e) {
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            $output->writeln(sprintf("<error>Error thrown during execution. %s</error>>", $e->getMessage()));
            return;
        }

        $this->entityHelper->logInfoMsg($message);
        $this->outputSuccessMsg($output, self::JOB_TYPE_SCAN_MISSING_PROFILES, $message);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function scanDbForAbandonedCarts(InputInterface $input, OutputInterface $output): void
    {
        $this->entityHelper->logInfoMsg(__METHOD__);
        $message = '';

        try {
            foreach ($this->shopContext->getAllActiveShopsList() as $shop) {
                $fromTime = $this->getFromDateTimeForGivenShop($shop)
                    ->sub($this->dateHelper->getDateIntervalFromIntervalSpec('PT60M'));
                $toTime = clone $fromTime;
                $fromTime->sub($this->dateHelper->getDateIntervalFromIntervalSpec('PT5M'));

                $sql = sprintf(
                    EI::ABANDONED_CART_INSERT_SQL,
                    $shop[EI::C_ID_SHOP],
                    $fromTime->format('Y-m-d H:i:s'),
                    $toTime->format('Y-m-d H:i:s')
                );
                $message .= $this->executeQueryAndGetResultString($sql, $shop[EI::C_ID_SHOP], 'Abandoned Carts');
            }
        } catch (Throwable $e) {
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            $output->writeln(sprintf("<error>Error thrown during execution. %s</error>>", $e->getMessage()));
            return;
        }

        $this->entityHelper->logInfoMsg($message);
        $this->outputSuccessMsg($output, self::JOB_TYPE_SCAN_AC, $message);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function scanDbForSilentSubscriptionUpdate(InputInterface $input, OutputInterface $output): void
    {
        $this->entityHelper->logInfoMsg(__METHOD__);
        $updated = $eventsCreated = 0;

        try {
            $subsNeedUpd = PsDb::getInstance()->executeS(EI::PROFILE_SQL_SUBSCRIBER_SELECT_NEEDING_UPDATE);
            if (is_array($subsNeedUpd) && ! empty($subsNeedUpd)) {
                PsDb::getInstance()->query(EI::PROFILE_SQL_SUBSCRIBER_UPDATE_NEEDING_UPDATE);
                $updated += count($subsNeedUpd);
                $eventsCreated += $this->entityHelper->registerSubsEventsForSubscribers($subsNeedUpd);
            }

            $customerNeedUp = PsDb::getInstance()->executeS(EI::PROFILE_SQL_CUSTOMER_SELECT_NEEDING_UPDATE);
            if (is_array($customerNeedUp) && ! empty($customerNeedUp)) {
                PsDb::getInstance()->query(EI::PROFILE_SQL_CUSTOMER_UPDATE_NEEDING_UPDATE);
                $updated += count($customerNeedUp);
                $eventsCreated += $this->entityHelper->registerSubsEventsForCustomers($customerNeedUp);
            }
        } catch (Throwable $e) {
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            $output->writeln(sprintf("<error>Error thrown during execution. %s</error>>", $e->getMessage()));
            return;
        }

        $message = sprintf(' Updated %d Profiles. Inserted %d Events.', $updated, $eventsCreated);
        $this->entityHelper->logInfoMsg($message);
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