<?php

namespace Apsis\One\Module;

use Apsis_one;
use Apsis\One\Entity\EntityInterface as EI;
use Db;
use Tab;
use Throwable;

class Uninstall extends AbstractSetup
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
            return $this->uninstallConfigurations() &&
                $this->uninstallHooks() &&
                $this->removeTables() &&
                $this->uninstallTabs();
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @return bool
     */
    protected function uninstallConfigurations(): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        try {
            return $this->configs->deleteGlobalKey() &&
                $this->configs->deleteProfileSyncFlagFromAllContext() &&
                $this->configs->deleteEventSyncFlagFromAllContext() &&
                $this->configs->deleteTrackingCodeFromAllContext() &&
                $this->configs->deleteInstallationConfigsFromAllContext() &&
                $this->configs->deleteApiTokenFromAllContext() &&
                $this->configs->deleteApiTokenExpiryFromAllContext() &&
                $this->configs->deleteDbCleanUpAfterFromAllContext() &&
                $this->configs->deleteProfileSynSizeFromAllContext();
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @return bool
     */
    protected function uninstallHooks(): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        try {
            $status = true;
            foreach ($this->module->helper->getAllAvailableHooks() as $hook) {
                if (! $this->module->unregisterHook($hook)) {
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
    protected function removeTables(): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        try {
        $db = Db::getInstance();
            return $db->execute('DROP TABLE IF EXISTS `' . $this->getTableWithDbPrefix(EI::T_PROFILE) . '`') &&
                $db->execute('DROP TABLE IF EXISTS `' . $this->getTableWithDbPrefix(EI::T_EVENT) . '`') &&
                $db->execute('DROP TABLE IF EXISTS `' . $this->getTableWithDbPrefix(EI::T_ABANDONED_CART) . '`');
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @return bool
     */
    protected function uninstallTabs(): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        try {
            $menuItems = array_merge([self::APSIS_MENU], EI::TABLES);
            foreach ($menuItems as $menuItem) {
                $tabId = (int) Tab::getIdFromClassName(self::LEGACY_CONTROLLER_CLASSES[$menuItem]);
                if (! $tabId) {
                    continue;
                }

                $tab = new Tab($tabId);
                $tab->delete();
            }
            return true;
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }
}
