<?php

namespace Apsis\One\Module;

use Apsis_one;
use Apsis\One\Helper\HelperInterface;
use Apsis\One\Context\ShopContext;
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

            return $this->installConfiguration() && $this->installHooks();
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @return bool
     */
    protected function installConfiguration(): bool
    {
        return $this->configs->saveGlobalKey();
    }

    /**
     * @return bool
     */
    protected function installHooks(): bool
    {
        $status = true;
        foreach ($this->module->helper->getAllAvailableHooks() as $hook) {
            if (! $this->module->registerHook($hook)) {
                $status = false;
            }
        }
        return $status;
    }
}
