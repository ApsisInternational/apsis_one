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
        $this->helper = $helper;
        $this->init($this);
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
            'min' => '1.7.8.0',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;
        $this->displayName = self::MODULE_DISPLAY_NAME;
        $this->description = 'Grow faster with the all-in-One marketing platform.';
        $this->confirmUninstall = 'Are you sure you want to uninstall?';
    }

    /**
     * @param bool $parent
     *
     * @return bool
     */
    public function install(bool $parent = false): bool
    {
        try {
            $this->helper->logInfoMsg($parent === false ? __METHOD__ :
                __METHOD__ . ' > ' . str_replace('Apsis_one', 'Module', __METHOD__));

            /** @var Install $installModule */
            $installModule = $this->helper->getService(HelperInterface::SERVICE_MODULE_INSTALL);
            return $parent === true ? parent::install() : $installModule->init($this);
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @param bool $parent
     *
     * @return bool
     */
    public function uninstall(bool $parent = false): bool
    {
        try {
            $this->helper->logInfoMsg($parent === false ? __METHOD__ :
                __METHOD__ . ' > ' . str_replace('Apsis_one', 'Module', __METHOD__));

            /** @var Uninstall $uninstallModule */
            $uninstallModule = $this->helper->getService(HelperInterface::SERVICE_MODULE_UNINSTALL);
            return $parent === true ? parent::uninstall() : $uninstallModule->init($this);
        } catch (Throwable $e) {
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
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
            return 'An error occurred, please check APSIS log file.';
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
     */
    public function hookActionObjectCustomerAddAfter(array $hookArgs): void
    {
        $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     */
    public function hookActionObjectCustomerUpdateAfter(array $hookArgs): void
    {
        $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     */
    public function hookActionObjectCustomerDeleteAfter(array $hookArgs): void
    {
        $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     */
    public function hookActionAuthentication(array $hookArgs): void
    {
        $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     */
    public function hookActionNewsletterRegistrationAfter(array $hookArgs): void
    {
        $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     */
    public function hookActionObjectAddressAddAfter(array $hookArgs): void
    {
        $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     */
    public function hookActionObjectAddressUpdateAfter(array $hookArgs): void
    {
        $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     */
    public function hookActionObjectAddressDeleteAfter(array $hookArgs): void
    {
        $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     */
    public function hookActionObjectProductCommentValidateAfter(array $hookArgs): void
    {
        $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     */
    public function hookActionWishlistAddProduct(array $hookArgs): void
    {
        $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     */
    public function hookActionObjectOrderAddAfter(array $hookArgs): void
    {
        $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     */
    public function hookActionObjectOrderUpdateAfter(array $hookArgs): void
    {
        $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     */
    public function hookActionCartUpdateQuantityBefore(array $hookArgs): void
    {
        $this->getHookProcessor()->processHook(__FUNCTION__, $hookArgs);
    }

    /**
     * @param array $hookArgs
     *
     * @return string
     */
    public function hookDisplayCustomerAccount(array $hookArgs): string
    {
        if (! $this->helper->isModuleEnabledForCurrentShop()) {
            return '';
        }

        $this->helper->logInfoMsg(__METHOD__);
        return 'YES';
        /**  See Method hookDisplayAdminCustomersForm() Class ps_emailsubscription
        $input = [];
        $this->context->smarty->assign(['input' => $input]);
        return $this->display(__FILE__, 'views/templates/admin/newsletter_subscribe.tpl');
        **/
    }
}
