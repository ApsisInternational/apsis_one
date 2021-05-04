<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use PrestaShop\ModuleLibServiceContainer\DependencyInjection\ServiceContainer;
use Apsis\One\Module\Install;
use Apsis\One\Module\Uninstall;
use Apsis\One\Module\Configuration;
use Apsis\One\Helper\LoggerHelper;
use Apsis\One\Context\PrestashopContext;

class Apsis_one extends Module
{
    /**
     * @var ServiceContainer
     */
    private $serviceContainer;

    /**
     * @var LoggerHelper
     */
    private $loggerHelper;

    /**
     * Apsis_one constructor.
     */
    public function __construct()
    {
        $this->name = 'apsis_one';
        $this->tab = 'advertising_marketing';
        $this->version = '1.0.0';
        $this->author = 'APSIS';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = [
            'min' => '1.7.7.3',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('APSIS One Integration');
        $this->description = $this->l('Grow faster with the all-in-One marketing platform.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->loggerHelper = $this->getService('apsis_one.helper.logger');
    }

    /**
     * @return bool
     */
    public function install()
    {
        try {
            /** @var Install $installModule */
            $installModule = $this->getService('apsis_one.module.install');
            return parent::install() && $installModule->init();
            //See if we should enable module for all shops
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        try {
            /** @var Uninstall $uninstallModule */
            $uninstallModule = $this->getService('apsis_one.module.uninstall');
            return parent::uninstall() && $uninstallModule->init();
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isModuleEnabledForCurrentShop()
    {
        try {
            return (bool) parent::isEnabled($this->name);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int $idShopGroup
     * @param null $idShop
     *
     * @return bool
     */
    public function isModuleEnabledForContext($idShopGroup = null, $idShop = null)
    {
        $active = false;

        try {
            /** @var PrestashopContext $prestaShopContext */
            $prestaShopContext = $this->getService('apsis_one.context.prestashop');

            if ($idShop) { // Need to check if enabled for the shop itself
                $shopList = [$idShop];
            } elseif ($idShopGroup) { // Need to check if enabled for minimum one shop under the group
                if (empty($list = $prestaShopContext->getShopListGroupedByGroup()) || empty($list[$idShopGroup])) {
                    return $active;
                }
                $shopList = $list[$idShopGroup];
            } else { //Need to check if module is enabled for least one shop
                if (empty($shopList = $prestaShopContext->getAllActiveShopIdsAsList())) {
                    return $active;
                }
            }

            $in = implode(',', array_map('intval', $shopList));
            $select = 'SELECT `id_module` FROM `' . _DB_PREFIX_ . 'module_shop`';
            $where = 'WHERE `id_module` = ' . Module::getModuleIdByName($this->name) . ' AND `id_shop` IN (' . $in . ')';

            if (Db::getInstance()->getValue($select . ' ' . $where)) {
                $active = true;
            }
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
        }

        return $active;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        try {
            /** @var Configuration $configurationModule */
            $configurationModule = $this->getService('apsis_one.module.configuration');
            return $configurationModule->showConfigurations();
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return '';
        }
    }

    /**
     * @param string $serviceName
     *
     * @return object|null
     */
    public function getService(string $serviceName)
    {
        if ($this->serviceContainer === null) {
            $this->serviceContainer = new ServiceContainer($this->name, $this->getLocalPath());
        }
        return $this->serviceContainer->getService($serviceName);
    }
}