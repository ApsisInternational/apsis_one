<?php

namespace Apsis\One\Module;

use Apsis_one;
use Db;
use Exception;

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
            return $this->uninstallConfigurations() && $this->uninstallHooks() && $this->removeTables();
        } catch (Exception $e) {
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

        return $this->configs->deleteGlobalKey() &&
            $this->configs->deleteProfileSyncFlagFromAllContext() &&
            $this->configs->deleteEventSyncFlagFromAllContext() &&
            $this->configs->deleteTrackingCodeFromAllContext() &&
            $this->configs->deleteInstallationConfigsFromAllContext() &&
            $this->configs->deleteApiTokenFromAllContext() &&
            $this->configs->deleteApiTokenExpiryFromAllContext() &&
            $this->configs->deleteDbCleanUpAfterFromAllContext() &&
            $this->configs->deleteProfileSynSizeFromAllContext();
    }

    /**
     * @return bool
     */
    protected function uninstallHooks(): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        $status = true;
        foreach ($this->module->helper->getAllAvailableHooks() as $hook) {
            if (! $this->module->unregisterHook($hook)) {
                $status = false;
            }
        }
        return $status;
    }

    /**
     * @return bool
     */
    protected function removeTables(): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        $db = Db::getInstance();

        return $db->execute('DROP TABLE IF EXISTS `' . $this->addPrefix(self::MODULE_TABLE_PROFILE) . '`') &&
            $db->execute('DROP TABLE IF EXISTS `' . $this->addPrefix(self::MODULE_TABLE_EVENT) . '`') &&
            $db->execute('DROP TABLE IF EXISTS `' . $this->addPrefix(self::MODULE_TABLE_ABANDONED_CART) . '`');
    }
}
