<?php

namespace Apsis\One\Command;

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
                case self::JOB_TYPE_CLEANUP:
                    $this->outputSuccessMsg($output, self::JOB_TYPE_CLEANUP);
                    break;
                case self::JOB_TYPE_AC:
                    $this->outputSuccessMsg($output, self::JOB_TYPE_AC);
                    break;
                case self::JOB_TYPE_SCAN_SUBS_UPDATE:
                    $this->outputSuccessMsg(
                        $output,
                        self::JOB_TYPE_SCAN_SUBS_UPDATE . $this->scanDbForSilentSubscriptionUpdate($output)
                    );
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
     * @param OutputInterface $output
     *
     * @return string
     */
    private function scanDbForSilentSubscriptionUpdate(OutputInterface $output): string
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
            $output->writeln($e->getMessage());
        }

        $message = sprintf(' Updated %d Profiles. Inserted %d Events.', $updated, $eventsCreated);
        $this->entityHelper->logInfoMsg($message);
        return $message;
    }
}
