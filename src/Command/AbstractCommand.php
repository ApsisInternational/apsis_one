<?php

namespace Apsis\One\Command;

use Apsis\One\Context\ShopContext;
use Apsis\One\Helper\DateHelper;
use Context;
use PrestaShop\PrestaShop\Adapter\LegacyContextLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Apsis\One\Helper\EntityHelper;
use Throwable;

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
     * @var EntityHelper
     */
    protected $entityHelper;

    /**
     * @var DateHelper
     */
    protected $dateHelper;

    /**
     * @var ShopContext
     */
    protected $shopContext;

    /**
     * @var LegacyContextLoader
     */
    protected $legacyContextLoader;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    abstract protected function processCommand(InputInterface $input, OutputInterface $output): ?int;

    /**
     * @param null $name
     */
    public function __construct($name = null)
    {
        $this->entityHelper = new EntityHelper();
        $this->dateHelper = new DateHelper();
        $this->shopContext = new ShopContext($this->dateHelper);
        $this->legacyContextLoader = new LegacyContextLoader(Context::getContext());

        // Load generic context to start with.
        // @toDo load real shopId and shopGroupId for each sync
        //$this->legacyContextLoader->loadGenericContext();

        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        try {
            $this->setName($this->commandName)
                ->addArgument(self::ARG_REQ_JOB, InputArgument::REQUIRED, $this->argumentReqDesc)
                ->setDescription($this->commandDesc)
                ->setHelp($this->commandHelp);
        } catch (Throwable $e) {
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        try {
            $output->writeln($this->processorMsg);

            if (! $this->lock()) {
                $output->writeln(sprintf(self::MSG_ALREADY_RUNNING, $this->commandName));

                return 0;
            }

            return $this->processCommand($input, $output);
        } catch (Throwable $e) {
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            $output->writeln($e->getMessage());
            return 0;
        }
    }

    /**
     * @param OutputInterface $output
     * @param string $type
     * @param string $msg
     *
     * @return void
     */
    protected function outputSuccessMsg(OutputInterface $output, string $type, string $msg): void
    {
        try {
            $output->writeln(sprintf(self::MSG_SUCCESS , $type, $msg));
        } catch (Throwable $e) {
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            $output->writeln($e->getMessage());
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function outputErrorMsg(InputInterface $input, OutputInterface $output): void
    {
        try {
            $output->writeln(sprintf(self::MSG_ERROR, (string) $input->getArgument(self::ARG_REQ_JOB)));
        } catch (Throwable $e) {
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            $output->writeln($e->getMessage());
        }
    }
}
