<?php

namespace Apsis\One\Command;

use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        switch ($input->getArgument(self::ARG_REQ_JOB)) {
            case self::JOB_TYPE_CLEANUP:
                $this->outputSuccessMsg($output, self::JOB_TYPE_CLEANUP);
                break;
            case self::JOB_TYPE_AC:
                $this->outputSuccessMsg($output, self::JOB_TYPE_AC);
                break;
            default:
                $this->outputErrorMsg($input, $output);
        }

        $this->release();

        return 0;
    }
}