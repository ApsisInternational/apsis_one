<?php

namespace Apsis\One\Module;

use Apsis_one;
use Apsis\One\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use Apsis\One\Helper\HelperInterface;
use Apsis\One\Context\ShopContext;
use Apsis\One\Model\EntityInterface as EI;
use Apsis\One\Helper\HelperInterface as HI;
use Db;
use Language;
use Tab;
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

            return $this->createTables() &&
                $this->populateTables() &&
                $this->installConfigurations() &&
                $this->installTabs() &&
                $this->module->install(true) &&
                $this->installHooks();
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

        try {
            return $this->configs->saveGlobalKey();
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @return bool
     */
    protected function installHooks(): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);
        $status = true;

        try {
            foreach ($this->module->helper->getAllAvailableHooks() as $hook) {
                if (! $this->module->registerHook($hook)) {
                    $status = false;
                }
            }
            return $status;
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @return bool
     */
    protected function createTables(): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        try {
            $db = Db::getInstance();
            return $this->createProfileTable($db) &&
                $this->createEventTable($db) &&
                $this->createAbandonedCartTable($db);
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @return bool
     */
    protected function populateTables(): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        try {
            return $this->populateProfileTable() && $this->populateEventTable();
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @return bool
     */
    protected function installTabs(): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        try {
            //First create parent menu item and fetch id
            if (! $this->createTab(self::APSIS_MENU)) {
                return false;
            }

            //Create sub menu items under given parent menu
            $parentId = (new Tab(Tab::getIdFromClassName(self::APSIS_MENU)))->id;
            $status = true;
            foreach (array_merge(EI::TABLES, [self::APSIS_CONFIG_TAB]) as $menuItem) {
                if (! $this->createTab($menuItem, $parentId)) {
                    $status = false;
                }
            }

            return $status;
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @param Db $db
     *
     * @return bool
     */
    protected function createProfileTable(Db $db): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        try {
            $db->execute(sprintf('DROP TABLE IF EXISTS `%s`;', $this->getTableWithDbPrefix(EI::T_PROFILE)));
            $sql = '
            CREATE TABLE IF NOT EXISTS `' . $this->getTableWithDbPrefix(EI::T_PROFILE) . '` (
                `' . EI::C_ID_PROFILE . '` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `' . EI::C_ID_INTEGRATION . '` varchar(36) NOT NULL,
                `' . EI::C_ID_SHOP . '` int(11) unsigned NOT NULL,
                `' . EI::C_EMAIL . '` varchar(255) NOT NULL,
                `' . EI::C_ID_CUSTOMER . '` int(10) unsigned NOT NULL DEFAULT \'0\',
                `' . EI::C_ID_NEWSLETTER . '` int(10) unsigned NOT NULL DEFAULT \'0\',
                `' . EI::C_IS_CUSTOMER . '` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                `' . EI::C_IS_GUEST . '` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                `' . EI::C_IS_NEWSLETTER . '` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                `' . EI::C_IS_OFFERS . '` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                `' . EI::C_PROFILE_DATA . '` JSON NOT NULL,
                `' . EI::C_SYNC_STATUS . '` smallint(6) unsigned NOT NULL DEFAULT \'1\',
                `' . EI::C_ERROR_MSG . '` varchar(255) NOT NULL DEFAULT \'\',
                `' . EI::C_DATE_UPD . '` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`' . EI::C_ID_PROFILE . '`),
                UNIQUE KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_ID_INTEGRATION) . '` (`' . EI::C_ID_INTEGRATION . '`),
                UNIQUE KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_EMAIL . '_' . EI::C_ID_SHOP) . '` (`' . EI::C_EMAIL . '`, `' . EI::C_ID_SHOP . '`),
                KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_ID_CUSTOMER) . '` (`' . EI::C_ID_CUSTOMER . '`),
                KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_ID_NEWSLETTER) . '` (`' . EI::C_ID_NEWSLETTER . '`),
                KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_ID_SHOP) . '` (`' . EI::C_ID_SHOP . '`),
                KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_IS_CUSTOMER) . '` (`' . EI::C_IS_CUSTOMER . '`),
                KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_IS_GUEST) . '` (`' . EI::C_IS_GUEST . '`),
                KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_IS_NEWSLETTER) . '` (`' . EI::C_IS_NEWSLETTER . '`),
                KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_IS_OFFERS) . '` (`' . EI::C_IS_OFFERS . '`),
                KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_SYNC_STATUS) . '` (`' . EI::C_SYNC_STATUS . '`),
                KEY `' . $this->getIndex(EI::T_PROFILE, EI::C_DATE_UPD) . '` (`' . EI::C_DATE_UPD . '`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8';

            return $db->execute($sql);
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @param Db $db
     *
     * @return bool
     */
    protected function createEventTable(Db $db): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        try {
            $db->execute(sprintf('DROP TABLE IF EXISTS `%s`;', $this->getTableWithDbPrefix(EI::T_EVENT)));
            $sql = '
            CREATE TABLE IF NOT EXISTS `' . $this->getTableWithDbPrefix(EI::T_EVENT) . '` (
                `' . EI::C_ID_EVENT . '` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `' . EI::C_ID_PROFILE . '` int(10) unsigned NOT NULL,
                `' . EI::C_ID_SHOP . '` int(11) unsigned NOT NULL,
                `' . EI::C_ID_ENTITY_PS . '` int(10) unsigned NOT NULL,
                `' . EI::C_EVENT_TYPE . '` smallint(6) unsigned NOT NULL,
                `' . EI::C_EVENT_DATA . '` JSON NOT NULL,
                `' . EI::C_SYNC_STATUS . '` smallint(6) unsigned NOT NULL DEFAULT \'1\',
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
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @param Db $db
     *
     * @return bool
     */
    protected function createAbandonedCartTable(Db $db): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        try {
            $db->execute(sprintf('DROP TABLE IF EXISTS `%s`;', $this->getTableWithDbPrefix(EI::T_ABANDONED_CART)));
            $sql = '
                CREATE TABLE IF NOT EXISTS `' . $this->getTableWithDbPrefix(EI::T_ABANDONED_CART) . '` (
                    `' . EI::C_ID_AC . '` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `' . EI::C_ID_PROFILE . '` int(10) unsigned NOT NULL,
                    `' . EI::C_ID_SHOP . '` int(11) unsigned NOT NULL,
                    `' . EI::C_ID_CART . '` int(10) unsigned NOT NULL,
                    `' . EI::C_CART_DATA . '` JSON NOT NULL,
                    `' . EI::C_TOKEN . '` varchar(36) NOT NULL,
                    `' . EI::C_DATE_ADD . '` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`' . EI::C_ID_AC . '`),
                    KEY `' . $this->getIndex(EI::T_ABANDONED_CART, EI::C_ID_PROFILE) . '` (`' . EI::C_ID_PROFILE . '`),
                    KEY `' . $this->getIndex(EI::T_ABANDONED_CART, EI::C_ID_SHOP) . '` (`' . EI::C_ID_SHOP . '`),
                    KEY `' . $this->getIndex(EI::T_ABANDONED_CART, EI::C_ID_CART) . '` (`' . EI::C_ID_CART . '`),
                    KEY `' . $this->getIndex(EI::T_ABANDONED_CART, EI::C_DATE_ADD) . '` (`' . EI::C_DATE_ADD . '`)
                ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8';
            return $db->execute($sql);
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @param string $menuItem
     * @param int $parentId
     *
     * @return bool
     */
    protected function createTab(string $menuItem, int $parentId = 0): bool
    {
        try {
            $tab = new Tab();
            $tab->active = $tab->enabled = 1;
            $tab->class_name = self::LEGACY_CONTROLLER_CLASSES[$menuItem];
            $tab->name = [];
            $wording = self::MODULE_DISPLAY_NAME;
            $routeName = '';

            if ($menuItem === self::APSIS_CONFIG_TAB) {
                $wording = self::MODULE_CONFIG_TAB;
                $routeName = HI::MODULE_CONFIG_ROUTE;
            } elseif (isset(EI::T_LABEL_MAPPINGS[$menuItem], GridDefinitionFactoryInterface::GRID_ROUTES_LIST_MAP[$menuItem])) {
                $wording = EI::T_LABEL_MAPPINGS[$menuItem];
                $routeName = GridDefinitionFactoryInterface::GRID_ROUTES_LIST_MAP[$menuItem];
            }

            $tab->wording = $wording;
            $tab->wording_domain = 'Admin.Navigation.Menu';
            foreach (Language::getLanguages() as $lang) {
                $tab->name[$lang['id_lang']] = $tab->wording;
            }
            $tab->route_name = $routeName;
            $tab->id_parent = empty($parentId) ? (int) Tab::getIdFromClassName('IMPROVE') : $parentId;
            $tab->icon = empty($parentId) ? 'account_circle' : '';
            $tab->module = self::MODULE_NAME;
            return $tab->save();
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @return bool
     */
    protected function populateProfileTable(): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        try {
            $status = true;
            foreach (self::T_PROFILE_MIGRATE_DATA_FROM_TABLES as $sql) {
                $status = $status && Db::getInstance()->execute($sql);
            }
            return $status;
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @return bool
     */
    protected function populateEventTable(): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        try {
            $status = true;
            foreach (self::T_EVENT_MIGRATE_HISTORICAL_EVENTS_SQL as $table => $sql) {
                $status = $status && Db::getInstance()->execute(sprintf($sql, $table, EI::SS_JUSTIN));
            }
            return $status;
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }
}
