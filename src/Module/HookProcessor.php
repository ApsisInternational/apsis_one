<?php

namespace Apsis\One\Module;

use Apsis_one;

class HookProcessor extends AbstractSetup
{
    /**
     * @param Apsis_one $module
     *
     * @return void
     */
    public function init(Apsis_one $module): void
    {
        $this->module = $module;
    }

    /**
     * @param string $hookName
     * @param array $hookArgs
     *
     * @return bool
     */
    public function processHook(string $hookName, array $hookArgs): bool
    {
        $this->module->helper->logMsg($hookName);
        return true;
    }
}