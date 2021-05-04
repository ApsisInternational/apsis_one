<?php

use Apsis\One\Controller\AbstractApiController;
use Apsis\One\Repository\ConfigurationRepository;

class apsis_OneApiinstallationconfigModuleFrontController extends AbstractApiController
{
    const QUERY_PARAM_RESET = 'reset';

    /**
     * @var string
     */
    protected $validRequestMethod = AbstractApiController::HTTP_POST;

    /**
     * @var array
     */
    protected $validBodyParams = [
        ConfigurationRepository::INSTALLATION_CONFIG_CLIENT_ID => AbstractApiController::DATA_TYPE_STRING,
        ConfigurationRepository::INSTALLATION_CONFIG_CLIENT_SECRET => AbstractApiController::DATA_TYPE_STRING,
        ConfigurationRepository::INSTALLATION_CONFIG_SECTION_DISCRIMINATOR => AbstractApiController::DATA_TYPE_STRING,
        ConfigurationRepository::INSTALLATION_CONFIG_KEYSPACE_DISCRIMINATOR => AbstractApiController::DATA_TYPE_STRING,
        ConfigurationRepository::INSTALLATION_CONFIG_API_BASE_URL  => AbstractApiController::DATA_TYPE_URL
    ];

    /**
     * @var array
     */
    protected $validQueryParams = [
        AbstractApiController::QUERY_PARAM_CONTEXT_IDS => AbstractApiController::DATA_TYPE_STRING
    ];

    /**
     * @var array
     */
    protected $optionalQueryParams = [
        self::QUERY_PARAM_RESET => AbstractApiController::DATA_TYPE_INT
    ];

    /**
     * @var array
     */
    protected $optionalQueryParamIgnoreRelations = [
        self::QUERY_PARAM_RESET => [AbstractApiController::PARAM_TYPE_BODY => [AbstractApiController::PARAM_TYPE_BODY]]
    ];

    public function init()
    {
        try {
            parent::init();
            $this->handleRequest();
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    protected function handleRequest()
    {
        try {
            $this->checkForResetParam();
            $this->saveInstallationConfigs();
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    private function saveInstallationConfigs()
    {
        try {
            if ($this->configurationRepository->saveInstallationConfigs($this->bodyParams, $this->groupId, $this->shopId)) {
                $this->configurationRepository
                    ->saveProfileSyncFlag(ConfigurationRepository::CONFIG_FLAG_YES, $this->groupId, $this->shopId);
                $this->configurationRepository
                    ->saveEventSyncFlag(ConfigurationRepository::CONFIG_FLAG_YES, $this->groupId, $this->shopId);
                $this->exitWithResponse($this->generateResponse(AbstractApiController::HTTP_CODE_204));
            } else {
                $msg = 'Unable to save some configurations.';
                $this->exitWithResponse($this->generateResponse(AbstractApiController::HTTP_CODE_500, [], $msg));
            }
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    private function checkForResetParam()
    {
        try {
            if (isset($this->queryParams[self::QUERY_PARAM_RESET])) {
                //@todo also reset events and profiles
                if ($this->configurationRepository->disableFeaturesAndDeleteConfig($this->groupId, $this->shopId, true)) {
                    $this->exitWithResponse($this->generateResponse(AbstractApiController::HTTP_CODE_204));
                } else {
                    $msg = 'Unable to reset some configurations.';
                    $this->exitWithResponse($this->generateResponse(AbstractApiController::HTTP_CODE_500, [], $msg));
                }
            }
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }
}