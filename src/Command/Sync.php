<?php

namespace Apsis\One\Command;

use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        switch ($input->getArgument(self::ARG_REQ_JOB)) {
            case self::JOB_TYPE_PROFILE:
                $this->outputSuccessMsg($output, self::JOB_TYPE_PROFILE, '');
                break;
            case self::JOB_TYPE_EVENT:
                $this->outputSuccessMsg($output, self::JOB_TYPE_EVENT, '');
                break;
            default:
                $this->outputErrorMsg($input, $output);
        }

        $this->release();

        return 0;
    }
}