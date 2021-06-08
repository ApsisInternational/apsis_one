<?php

namespace Apsis\One\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command implements CommandInterface
{
    use LockableTrait;

    /**
     * @var string
     */
    protected $commandName;

    /**
     * @var string
     */
    protected $commandDesc;

    /**
     * @var string
     */
    protected $commandHelp;

    /**
     * @var string
     */
    protected $argumentReqDesc;

    /**
     * @var array
     */
    protected $processorMsg;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    abstract protected function processCommand(InputInterface $input, OutputInterface $output): ?int;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName($this->commandName)
            ->addArgument(self::ARG_REQ_JOB, InputArgument::REQUIRED, $this->argumentReqDesc)
            ->setDescription($this->commandDesc)
            ->setHelp($this->commandHelp);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $output->writeln($this->processorMsg);

        if (! $this->lock()) {
            $output->writeln(sprintf(self::MSG_ALREADY_RUNNING, $this->commandName));

            return 0;
        }

        return $this->processCommand($input, $output);
    }

    /**
     * @param OutputInterface $output
     * @param string $type
     *
     * @return void
     */
    protected function outputSuccessMsg(OutputInterface $output, string $type): void
    {
        $output->writeln(sprintf(self::MSG_SUCCESS , $type));
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function outputErrorMsg(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln(sprintf(self::MSG_ERROR, (string) $input->getArgument(self::ARG_REQ_JOB)));
    }
}