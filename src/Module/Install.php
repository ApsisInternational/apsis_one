<?php

namespace Apsis\One\Module;

use Apsis_one;
use Apsis\One\Helper\HelperInterface;
use Apsis\One\Context\ShopContext;
use Db;
use Exception;

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
        } catch (Exception $e) {
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

        $db->execute('DROP TABLE IF EXISTS ' . self::MODULE_TABLE_PROFILE);

        $sql = '
        CREATE TABLE IF NOT EXISTS `' . self::MODULE_TABLE_PROFILE . '` (
            `id_profile` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_integration` varchar(255) NOT NULL,
            `id_shop` int(11) unsigned NOT NULL,
            `email` varchar(255) NOT NULL,
            `id_customer` int(10) unsigned DEFAULT NULL,
            `id_subscription` int(10) unsigned DEFAULT NULL,
            `is_customer` tinyint(1) NOT NULL DEFAULT \'0\',
            `is_guest` tinyint(1) NOT NULL DEFAULT \'0\',
            `is_subscriber` tinyint(1) NOT NULL DEFAULT \'0\',
            `sync_status` smallint(6) NOT NULL DEFAULT \'0\',
            `error_message` varchar(255) NOT NULL DEFAULT \'\',
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id_profile`),
            KEY `APSIS_PROFILE_ID` (`id_profile`),
            KEY `APSIS_PROFILE_SHOP_ID` (`id_shop`),
            KEY `APSIS_PROFILE_CUSTOMER_ID` (`id_customer`),
            KEY `APSIS_PROFILE_SUBSCRIBER_ID` (`id_subscription`),
            KEY `APSIS_PROFILE_IS_SUBSCRIBER` (`is_subscriber`),
            KEY `APSIS_PROFILE_IS_CUSTOMER` (`is_customer`),
            KEY `APSIS_PROFILE_IS_GUEST` (`is_guest`),
            KEY `APSIS_PROFILE_EMAIL` (`email`),
            KEY `APSIS_PROFILE_SYNC_STATUS` (`sync_status`),
            KEY `APSIS_PROFILE_UPDATED_AT` (`updated_at`)
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

        $db->execute('DROP TABLE IF EXISTS ' . self::MODULE_TABLE_EVENT);

        $sql = '
        CREATE TABLE IF NOT EXISTS `' . self::MODULE_TABLE_EVENT . '` (
            `id_event` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_shop` int(11) unsigned NOT NULL,
            `email` varchar(255) NOT NULL,
            `id_profile` int(10) unsigned NOT NULL,
            `id_customer` int(10) unsigned DEFAULT NULL,
            `event_type` smallint(6) unsigned NOT NULL,
            `event_data` blob NOT NULL,
            `sub_event_data` blob NOT NULL,
            `sync_status` smallint(6) NOT NULL DEFAULT \'0\',
            `error_message` varchar(255) NOT NULL DEFAULT \'\',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id_event`),
            KEY `APSIS_EVENT_ID` (`id_event`),
            KEY `APSIS_EVENT_SHOP_ID` (`id_shop`),
            KEY `APSIS_EVENT_PROFILE_ID` (`id_profile`),
            KEY `APSIS_EVENT_CUSTOMER_ID` (`id_customer`),
            KEY `APSIS_EVENT_EVENT_TYPE` (`event_type`),
            KEY `APSIS_EVENT_EMAIL` (`email`),
            KEY `APSIS_EVENT_SYNC_STATUS` (`sync_status`),
            KEY `APSIS_EVENT_CREATED_AT` (`created_at`),
            KEY `APSIS_EVENT_UPDATED_AT` (`updated_at`)
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

        $db->execute('DROP TABLE IF EXISTS ' . self::MODULE_TABLE_ABANDONED_CART);

        $sql = '
        CREATE TABLE IF NOT EXISTS `' . self::MODULE_TABLE_ABANDONED_CART . '` (
            `id_abandonedcart` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_shop` int(11) unsigned NOT NULL,
            `id_cart` int(10) unsigned NOT NULL,
            `email` varchar(255) NOT NULL,
            `id_profile` int(10) unsigned NOT NULL,
            `id_customer` int(10) unsigned DEFAULT NULL,
            `cart_data` blob NOT NULL,
            `token` varchar(255) NOT NULL,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id_abandonedcart`),
            KEY `APSIS_ABANDONED_ID` (`id_abandonedcart`),
            KEY `APSIS_ABANDONED_CART_ID` (`id_cart`),
            KEY `APSIS_ABANDONED_SHOP_ID` (`id_shop`),
            KEY `APSIS_ABANDONED_CUSTOMER_ID` (`id_customer`),
            KEY `APSIS_ABANDONED_EMAIL` (`email`),
            KEY `APSIS_ABANDONED_PROFILE_ID` (`id_profile`),
            KEY `APSIS_ABANDONED_CREATED_AT` (`updated_at`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8';

        return $db->execute($sql);
    }
}
