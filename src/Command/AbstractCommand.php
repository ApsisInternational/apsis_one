<?php

namespace Apsis\One\Command;

use Apsis\One\Helper\HelperInterface as HI;
use Apsis\One\Context\ShopContext;
use Apsis\One\Helper\ModuleHelper;
use Apsis\One\Module\Configuration\Configs;
use Apsis\One\Helper\EntityHelper;
use PrestaShop\PrestaShop\Adapter\LegacyContextLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

abstract class AbstractCommand extends Command implements CommandInterface
{
    use LockableTrait;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

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
     * @var ModuleHelper
     */
    protected $moduleHelper;

    /**
     * @var Configs
     */
    protected $configs;

    /**
     * @var ShopContext
     */
    protected $shopContext;

    /**
     * @var LegacyContextLoader
     */
    protected $legacyContextLoader;

    /**
     * @var array
     */
    protected $installationConfigs;

    /**
     * @return int
     */
    abstract protected function processCommand(): int;

    /**
     * @param null $name
     */
    public function __construct($name = null)
    {
        $this->moduleHelper = new ModuleHelper();
        $this->legacyContextLoader = $this->moduleHelper
            ->getService(HI::SERVICE_PS_LEGACY_CONTEXT_LOADER, HI::FROM_CONTAINER_FD);
        $this->entityHelper = $this->moduleHelper->getService(HI::SERVICE_HELPER_ENTITY);
        $this->shopContext = $this->moduleHelper->getService(HI::SERVICE_CONTEXT_SHOP);
        $this->configs = $this->moduleHelper->getService(HI::SERVICE_MODULE_CONFIGS);

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
                $message = sprintf(self::MSG_ALREADY_RUNNING, $this->commandName);
                $this->entityHelper->logInfoMsg($message);
                $output->writeln('<info>' . $message . '</info>');

                return 0;
            }

            $this->input = $input;
            $this->output = $output;

            return $this->processCommand();
        } catch (Throwable $e) {
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            $output->writeln($e->getMessage());
            return 0;
        }
    }

    /**
     * @param string $jobCode
     * @param string $msg
     */
    protected function outputSuccessMsg(string $jobCode, string $msg): void
    {
        try {
            $message = sprintf(self::MSG_SUCCESS , $jobCode, $msg);
            $this->entityHelper->logInfoMsg($message);
            $this->output->writeln('<info>' . $message . '</info>');
        } catch (Throwable $e) {
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            $this->output->writeln($e->getMessage());
        }
    }

    /**
     */
    protected function outputInvalidJobErrorMsg(): void
    {
        try {
            $message = sprintf(self::MSG_INVALID_JOB, (string) $this->input->getArgument(self::ARG_REQ_JOB));
            $this->entityHelper->logInfoMsg($message);
            $this->output->writeln('<error>' . $message . '</error>');
        } catch (Throwable $e) {
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            $this->output->writeln($e->getMessage());
        }
    }

    /**
     * @param string $jobCode
     * @param string $err
     */
    protected function outputRuntimeErrorMsg(string $jobCode, string $err) : void
    {
        try {
            $message = sprintf(self::MSG_RUNTIME_ERR, $jobCode, $err);
            $this->entityHelper->logInfoMsg($message);
            $this->output->writeln('<error>' . $message . '</error>');
        } catch (Throwable $e) {
            $this->entityHelper->logErrorMsg(__METHOD__, $e);
            $this->output->writeln($e->getMessage());
        }
    }

    /**
     * @param int $shopId
     * @param string $feature
     *
     * @return string
     */
    protected function isModuleAndFeatureActiveAndConnected(int $shopId, string $feature): ?string
    {
        if (! $this->moduleHelper->isModuleEnabledForContext(null, $shopId)) {
            return sprintf("\nSkipping for Shop ID {%d}, Module is disabled.", $shopId);
        }

        if (empty($configs = $this->configs->getInstallationConfigs(null, $shopId)) ||
            $this->configs->isAnyClientConfigMissing($configs, null, $shopId)
        ) {
            return sprintf("\nSkipping for Shop ID {%d}, Module is not connected to JUSTIN.", $shopId);
        }

        $this->installationConfigs = $configs;

        if ($feature === self::JOB_TYPE_PROFILE && ! $this->configs->getProfileSyncFlag(null, $shopId)) {
            return sprintf("\nSkipping for Shop ID {%d}, Profile sync feature is not active", $shopId);
        }

        if ($feature === self::JOB_TYPE_EVENT && ! $this->configs->getEventSyncFlag(null, $shopId)) {
            return sprintf("\nSkipping for Shop ID {%d}, Event sync feature is not active", $shopId);
        }

        return null;
    }

    /**
     * @param int $shopId
     */
    protected function loadGenericContext(int $shopId): void
    {
        $this->legacyContextLoader->loadGenericContext(get_class($this), null, null, $shopId);
    }
}
