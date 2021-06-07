<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use Apsis\One\Module\Install;
use Apsis\One\Module\SetupInterface;
use Apsis\One\Module\Uninstall;
use Apsis\One\Module\Configuration;
use Apsis\One\Module\HookProcessor;
use Apsis\One\Helper\ModuleHelper;
use Apsis\One\Helper\HelperInterface;

class Apsis_one extends Module implements SetupInterface
{
    /**
     * @var ModuleHelper
     */
    public $helper;

    /**
     * @var HookProcessor
     */
    public $hookProcessor;

    /**
     * Apsis_one constructor.
     *
     * @param ModuleHelper $helper
     */
    public function __construct(ModuleHelper $helper)
    {
        $this->init($this);
        $this->helper = $helper;

        parent::__construct();
    }

    /**
     * @param Apsis_one $module
     *
     * @return void
     */
    public function init(Apsis_one $module): void
    {
        $this->name = self::MODULE_NAME;
        $this->tab = 'advertising_marketing';
        $this->version = self::MODULE_VERSION;
        $this->author = 'APSIS';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = [
            'min' => '1.7.7.3',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;
        $this->displayName = $this->l('APSIS One Integration');
        $this->description = $this->l('Grow faster with the all-in-One marketing platform.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    /**
     * @return bool
     */
    public function install(): bool
    {
        try {
            $this->helper->logInfoMsg(__METHOD__);

            /** @var Install $installModule */
            $installModule = $this->helper->getService(HelperInterface::SERVICE_MODULE_INSTALL);
            return parent::install() && $installModule->init($this);
        } catch (Exception $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @return bool
     */
    public function uninstall(): bool
    {
        try {
            $this->helper->logInfoMsg(__METHOD__);

            /** @var Uninstall $uninstallModule */
            $uninstallModule = $this->helper->getService(HelperInterface::SERVICE_MODULE_UNINSTALL);
            return parent::uninstall() && $uninstallModule->init($this);
        } catch (Exception $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        try {
            /** @var Configuration $configurationModule */
            $configurationModule = $this->helper->getService(HelperInterface::SERVICE_MODULE_ADMIN_CONFIGURATION);
            return $configurationModule->init($this);
        } catch (Exception $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
            return '';
        }
    }

    /**
     * @return HookProcessor|null
     */
    protected function getHookProcessor(): ?HookProcessor
    {
        if ($this->hookProcessor === null) {
            $this->hookProcessor = $this->helper->getService(HelperInterface::SERVICE_MODULE_HOOK_PROCESSOR);
            $this->hookProcessor->init($this);
        }
        return $this->hookProcessor;
    }

    /**
     * @param array $hookArgs
     *
     * @return bool
     */
    public function hookActionObjectCustomerAddAfter(array $hookArgs): bool
    {
        return $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     *
     * @return bool
     */
    public function hookActionObjectCustomerUpdateAfter(array $hookArgs): bool
    {
        return $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     *
     * @return bool
     */
    public function hookActionObjectCustomerDeleteAfter(array $hookArgs): bool
    {
        return $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     *
     * @return bool
     */
    public function hookActionAuthentication(array $hookArgs): bool
    {
        return $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     *
     * @return bool
     */
    public function hookDisplayCustomerAccount(array $hookArgs): bool
    {
        return $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     *
     * @return bool
     */
    public function hookActionNewsletterRegistrationAfter(array $hookArgs): bool
    {
        return $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     *
     * @return bool
     */
    public function hookActionObjectProductCommentValidateAfter(array $hookArgs): bool
    {
        return $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     *
     * @return bool
     */
    public function hookActionWishlistAddProduct(array $hookArgs): bool
    {
        return $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     *
     * @return bool
     */
    public function hookActionValidateOrder(array $hookArgs): bool
    {
        return $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     *
     * @return bool
     */
    public function hookActionCartUpdateQuantityBefore(array $hookArgs): bool
    {
        return $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }
}