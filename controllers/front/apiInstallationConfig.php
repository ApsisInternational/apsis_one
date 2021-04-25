<?php

use Apsis\One\Controller\AbstractApiController;
use Apsis\One\Repository\ConfigurationRepository;

class apsis_OneApiInstallationConfigModuleFrontController extends AbstractApiController
{
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
        ConfigurationRepository::INSTALLATION_CONFIG_ACCOUNT_ID => AbstractApiController::DATA_TYPE_STRING,
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
        AbstractApiController::QUERY_PARAM_RESET => AbstractApiController::DATA_TYPE_INT
    ];

    public function init()
    {
        parent::init();

        $this->processRequest();
    }

    private function processRequest()
    {
        $this->configurationRepository
            ->saveInstallationConfigs($this->bodyParams, $this->groupId, $this->shopId) &&
        $this->configurationRepository
            ->saveProfileSyncFlag(ConfigurationRepository::CONFIG_FLAG_YES, $this->groupId, $this->shopId) &&
        $this->configurationRepository
            ->saveEventSyncFlag(ConfigurationRepository::CONFIG_FLAG_YES, $this->groupId, $this->shopId) ?
            $this->exitWithResponse($this->generateResponse(201)) :
            $this->exitWithResponse($this->generateResponse(500, [], 'Unable to save configurations.'));
    }
}