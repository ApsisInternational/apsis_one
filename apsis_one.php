<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use PrestaShop\ModuleLibServiceContainer\DependencyInjection\ServiceContainer;
use Apsis\One\Module\Install;
use Apsis\One\Module\Uninstall;
use Apsis\One\Module\Configuration;

class Apsis_one extends Module
{
    /**
     * @var ServiceContainer
     */
    private $serviceContainer;

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

        parent::__construct();

        $this->displayName = $this->l('APSIS One Integration');
        $this->description = $this->l('Grow faster with the all-in-One marketing platform');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if ($this->serviceContainer === null) {
            $this->serviceContainer = new ServiceContainer($this->name, $this->getLocalPath());
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        /** @var Install $installModule */
        $installModule = $this->getService('apsis_one.module.install');

        return parent::install() && $installModule->init();
    }

    public function uninstall()
    {
        /** @var Uninstall $uninstallModule */
        $uninstallModule = $this->getService('apsis_one.module.uninstall');

        return parent::uninstall() && $uninstallModule->init();
    }

    public function enable($force_all = false)
    {
        return parent::enable($force_all);
    }

    public function disable($force_all = false)
    {
        return parent::disable($force_all);
    }

    public function getContent()
    {
        /** @var Configuration $configurationModule */
        $configurationModule = $this->getService('apsis_one.module.configuration');
        return $configurationModule->showConfigurations();
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