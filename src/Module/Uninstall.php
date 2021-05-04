<?php

namespace Apsis\One\Module;

use Apsis\One\Helper\LoggerHelper;
use Apsis\One\Repository\ConfigurationRepository;
use Exception;

class Uninstall
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
        try {
            return $this->uninstallConfiguration();
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
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
            $this->configurationRepository->deleteApiTokenForAllContext() &&
            $this->configurationRepository->deleteApiTokenExpiryForAllContext() &&
            $this->configurationRepository->deleteDbCleanUpAfterForAllContext() &&
            $this->configurationRepository->deleteProfileSynSizeForAllContext();
    }
}
