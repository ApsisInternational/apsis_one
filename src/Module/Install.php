<?php

namespace Apsis\One\Module;

use Apsis\One\Helper\LoggerHelper;
use Apsis\One\Repository\ConfigurationRepository;
use Exception;

class Install
{
    /**
     * @var ConfigurationRepository
     */
    protected $configurationRepository;

    /**
     * @var LoggerHelper
     */
    protected $loggerHelper;

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
        try {
            return $this->installConfiguration();
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    private function installConfiguration()
    {
        return $this->configurationRepository->saveGlobalKey();
    }
}
