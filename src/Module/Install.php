<?php

namespace Apsis\One\Module;

use Apsis\One\Helper\LoggerHelper;
use Apsis\One\Repository\ConfigurationRepository;

class Install
{
    /**
     * @var ConfigurationRepository
     */
    private $configurationRepository;

    /**
     * @var LoggerHelper
     */
    private $loggerHelper;

    /**
     * Install constructor.
     *
     * @param ConfigurationRepository $configurationRepository
     * @param LoggerHelper $loggerHelper
     */
    public function __construct(
        ConfigurationRepository $configurationRepository,
        LoggerHelper $loggerHelper
    ) {
        $this->configurationRepository = $configurationRepository;
        $this->loggerHelper = $loggerHelper;
    }

    /**
     * @return bool
     */
    public function init()
    {
        return $this->installConfiguration();
    }

    /**
     * @return bool
     */
    private function installConfiguration()
    {
        return $this->configurationRepository->saveGlobalKey();
    }
}
