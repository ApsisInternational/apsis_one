<?php

namespace Apsis\One\Module;

use Apsis\One\Helper\LoggerHelper;
use Apsis\One\Repository\ConfigurationRepository;

class Configuration
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
     * @return string
     */
    public function showConfigurations()
    {
        return 'abc';
    }
}