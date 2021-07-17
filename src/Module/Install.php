<?php

namespace Apsis\One\Module;

use Apsis_one;
use Apsis\One\Helper\HelperInterface;
use Apsis\One\Context\ShopContext;
use Apsis\One\Entity\EntityInterface as EI;
use Db;
use Throwable;

class Install extends AbstractSetup
{
    /**
     * @param Apsis_one $module
     *
     * @return bool
     */
    public function init(Apsis_one $module): bool
    {
        try {
            $this->module = $module;

            /** @var ShopContext $shopContext */
            $shopContext = $this->module->helper->getService(HelperInterface::SERVICE_CONTEXT_SHOP);
            if ($shopContext->isMultiShopFeatureActive()) {
                $shopContext->setContext();
            }

            return $this->installConfigurations() && $this->installHooks() && $this->createTables();
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @return bool
     */
    protected function installConfigurations(): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        return $this->configs->saveGlobalKey();
    }

    /**
     * @return bool
     */
    protected function installHooks(): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        $status = true;
        foreach ($this->module->helper->getAllAvailableHooks() as $hook) {
            if (! $this->module->registerHook($hook)) {
                $status = false;
            }
        }
        return $status;
    }

    /**
     * @return bool
     */
    protected function createTables(): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        $db = Db::getInstance();

        return $this->createProfileTable($db) && $this->createEventTable($db) && $this->createAbandonedCartTable($db);
    }

    /**
     * @param Db $db
     *
     * @return bool
     */
    protected function createProfileTable(Db $db): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        $sql = '
        CREATE TABLE IF NOT EXISTS `' . $this->getTableWithDbPrefix(EI::T_PROFILE) . '` (
            `' . EI::C_ID_PROFILE . '` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `' . EI::C_ID_INTEGRATION . '` varchar(36) NOT NULL,
            `' . EI::C_ID_SHOP . '` int(11) unsigned NOT NULL,
            `' . EI::C_EMAIL . '` varchar(255) NOT NULL,
            `' . EI::C_ID_ENTITY_PS . '` int(10) unsigned NOT NULL,
            `' . EI::C_IS_CUSTOMER . '` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
            `' . EI::C_IS_GUEST . '` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
            `' . EI::C_IS_SUBSCRIBER . '` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
            `' . EI::C_SYNC_STATUS . '` smallint(6) unsigned NOT NULL DEFAULT \'0\',
            `' . EI::C_ERROR_MSG . '` varchar(255) NOT NULL DEFAULT \'\',
            `' . EI::C_DATE_UPD . '` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`' . EI::C_ID_PROFILE . '`),
            UNIQUE KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_ID_INTEGRATION) . '` (`' . EI::C_ID_INTEGRATION . '`),
            UNIQUE KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_EMAIL) . '` (`' . EI::C_EMAIL . '`),
            KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_ID_ENTITY_PS) . '` (`' . EI::C_ID_ENTITY_PS . '`),
            KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_ID_SHOP) . '` (`' . EI::C_ID_SHOP . '`),
            KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_IS_CUSTOMER) . '` (`' . EI::C_IS_CUSTOMER . '`),
            KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_IS_GUEST) . '` (`' . EI::C_IS_GUEST . '`),
            KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_IS_SUBSCRIBER) . '` (`' . EI::C_IS_SUBSCRIBER . '`),
            KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_SYNC_STATUS) . '` (`' . EI::C_SYNC_STATUS . '`),
            KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_DATE_UPD) . '` (`' . EI::C_DATE_UPD . '`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8';

        return $db->execute($sql);
    }

    /**
     * @param Db $db
     *
     * @return bool
     */
    protected function createEventTable(Db $db): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        $sql = '
        CREATE TABLE IF NOT EXISTS `' . $this->getTableWithDbPrefix(EI::T_EVENT) . '` (
            `' . EI::C_ID_EVENT . '` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `' . EI::C_ID_PROFILE . '` int(10) unsigned NOT NULL,
            `' . EI::C_ID_SHOP . '` int(11) unsigned NOT NULL,
            `' . EI::C_ID_ENTITY_PS . '` int(10) unsigned NOT NULL,
            `' . EI::C_EVENT_TYPE . '` smallint(6) unsigned NOT NULL,
            `' . EI::C_EVENT_DATA . '` text NOT NULL,
            `' . EI::C_SUB_EVENT_DATA . '` text NOT NULL DEFAULT \'\',
            `' . EI::C_SYNC_STATUS . '` smallint(6) unsigned NOT NULL DEFAULT \'0\',
            `' . EI::C_ERROR_MSG . '` varchar(255) NOT NULL DEFAULT \'\',
            `' . EI::C_DATE_ADD . '` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `' . EI::C_DATE_UPD . '` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`' . EI::C_ID_EVENT . '`),
            KEY `' . $this->getIndex(EI::T_EVENT, EI::C_ID_PROFILE) . '` (`' . EI::C_ID_PROFILE . '`),
            KEY `' . $this->getIndex(EI::T_EVENT, EI::C_ID_SHOP) . '` (`' . EI::C_ID_SHOP . '`),
            KEY `' . $this->getIndex(EI::T_EVENT, EI::C_ID_ENTITY_PS) . '` (`' . EI::C_ID_ENTITY_PS . '`),
            KEY `' . $this->getIndex(EI::T_EVENT, EI::C_EVENT_TYPE) . '` (`' . EI::C_EVENT_TYPE . '`),
            KEY `' . $this->getIndex(EI::T_EVENT, EI::C_SYNC_STATUS) . '` (`' . EI::C_SYNC_STATUS . '`),
            KEY `' . $this->getIndex(EI::T_EVENT, EI::C_DATE_ADD) . '` (`' . EI::C_DATE_ADD . '`),
            KEY `' . $this->getIndex(EI::T_EVENT, EI::C_DATE_UPD) . '` (`' . EI::C_DATE_UPD . '`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8';

        return $db->execute($sql);
    }

    /**
     * @param Db $db
     *
     * @return bool
     */
    protected function createAbandonedCartTable(Db $db): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        $sql = '
        CREATE TABLE IF NOT EXISTS `' . $this->getTableWithDbPrefix(EI::T_ABANDONED_CART) . '` (
            `' . EI::C_ID_AC . '` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `' . EI::C_ID_PROFILE . '` int(10) unsigned NOT NULL,
            `' . EI::C_ID_SHOP . '` int(11) unsigned NOT NULL,
            `' . EI::C_ID_CART . '` int(10) unsigned NOT NULL,
            `' . EI::C_CART_DATA . '` text NOT NULL,
            `' . EI::C_TOKEN . '` varchar(36) NOT NULL,
            `' . EI::C_DATE_UPD . '` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`' . EI::C_ID_AC . '`),
            KEY `' . $this->getIndex(EI::T_ABANDONED_CART, EI::C_ID_PROFILE) . '` (`' . EI::C_ID_PROFILE . '`),
            KEY `' . $this->getIndex(EI::T_ABANDONED_CART, EI::C_ID_SHOP) . '` (`' . EI::C_ID_SHOP . '`),
            KEY `' . $this->getIndex(EI::T_ABANDONED_CART, EI::C_ID_CART) . '` (`' . EI::C_ID_CART . '`),
            KEY `' . $this->getIndex(EI::T_ABANDONED_CART, EI::C_DATE_UPD) . '` (`' . EI::C_DATE_UPD . '`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8';

        return $db->execute($sql);
    }
}
