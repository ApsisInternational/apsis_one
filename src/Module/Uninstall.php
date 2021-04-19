<?php

namespace Apsis\One\Module;

use Apsis\One\Helper\LoggerHelper;
use Apsis\One\Repository\ConfigurationRepository;

class Uninstall
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
     * Uninstall constructor.
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
        return $this->uninstallConfiguration();
    }

    /**
     * @return bool
     */
    private function uninstallConfiguration()
    {
        return $this->configurationRepository->deleteGlobalKey() &&
            $this->configurationRepository->deleteProfileSyncFlagFromAllContext() &&
            $this->configurationRepository->deleteEventSyncFlagFromAllContext() &&
            $this->configurationRepository->deleteTrackingCodeFromAllContext() &&
            $this->configurationRepository->deleteInstallationConfigsFromAllContext() &&
            $this->configurationRepository->deleteApiTokenForAllContext();
    }
}
