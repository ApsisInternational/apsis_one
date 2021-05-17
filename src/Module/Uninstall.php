<?php

namespace Apsis\One\Module;

use Apsis_one;
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
            return $this->uninstallConfiguration() && $this->uninstallHooks();
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    protected function uninstallConfiguration(): bool
    {
        return $this->configs->deleteGlobalKey() &&
            $this->configs->deleteProfileSyncFlagFromAllContext() &&
            $this->configs->deleteEventSyncFlagFromAllContext() &&
            $this->configs->deleteTrackingCodeFromAllContext() &&
            $this->configs->deleteInstallationConfigsFromAllContext() &&
            $this->configs->deleteApiTokenForAllContext() &&
            $this->configs->deleteApiTokenExpiryForAllContext() &&
            $this->configs->deleteDbCleanUpAfterForAllContext() &&
            $this->configs->deleteProfileSynSizeForAllContext();
    }

    /**
     * @return bool
     */
    protected function uninstallHooks(): bool
    {
        $status = true;
        foreach ($this->module->helper->getAllAvailableHooks() as $hook) {
            if (! $this->module->unregisterHook($hook)) {
                $status = false;
            }
        }
        return $status;
    }
}
