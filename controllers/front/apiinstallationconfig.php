<?php

use Apsis\One\Controller\AbstractApiController;
use Apsis\One\Model\SchemaInterface;
use Apsis\One\Module\SetupInterface;

class apsis_OneApiinstallationconfigModuleFrontController extends AbstractApiController
{
    /**
     * @inheritdoc
     */
    protected $validRequestMethod = self::VERB_POST;

    /**
     * @inheritdoc
     */
    protected $validBodyParams = [
        SetupInterface::INSTALLATION_CONFIG_CLIENT_ID => self::DATA_TYPE_STRING,
        SetupInterface::INSTALLATION_CONFIG_CLIENT_SECRET => self::DATA_TYPE_STRING,
        SetupInterface::INSTALLATION_CONFIG_SECTION_DISCRIMINATOR => self::DATA_TYPE_STRING,
        SetupInterface::INSTALLATION_CONFIG_KEYSPACE_DISCRIMINATOR => self::DATA_TYPE_STRING,
        SetupInterface::INSTALLATION_CONFIG_API_BASE_URL  => SchemaInterface::VALIDATE_FORMAT_URL_NOT_NULL
    ];

    /**
     * @inheritdoc
     */
    protected $validQueryParams = [self::QUERY_PARAM_CONTEXT_IDS => self::DATA_TYPE_STRING];

    /**
     * @inheritdoc
     */
    protected $optionalQueryParams = [self::QUERY_PARAM_RESET => self::DATA_TYPE_INT];

    /**
     * @inheritdoc
     */
    protected $optionalQueryParamIgnoreRelations = [
        self::QUERY_PARAM_RESET => [self::PARAM_TYPE_BODY => [self::PARAM_TYPE_BODY]]
    ];

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        try {
            parent::init();
            $this->handleRequest();
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    /**
     * @inheritdoc
     */
    protected function handleRequest(): void
    {
        try {
            $this->checkForResetParam();
            $this->saveInstallationConfigs();
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    /**
     * @return void
     */
    protected function saveInstallationConfigs(): void
    {
        try {
            $status = $this->configs->saveInstallationConfigs($this->bodyParams, $this->groupId, $this->shopId);
            if ($status) {
                $this->configs->saveProfileSyncFlag(SetupInterface::CONFIG_FLAG_YES, $this->groupId, $this->shopId);
                $this->configs->saveEventSyncFlag(SetupInterface::CONFIG_FLAG_YES, $this->groupId, $this->shopId);
                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_204));
            } else {
                $msg = 'Unable to save some configurations.';
                $this->module->helper->logDebugMsg(__METHOD__, ['info' => $msg]);
                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_500, [], $msg));
            }
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    /**
     * @return void
     */
    protected function checkForResetParam(): void
    {
        try {
            if (isset($this->queryParams[self::QUERY_PARAM_RESET])) {
                // TODO: also reset events and profiles
                if ($this->configs->disableSyncsClearConfigs($this->groupId, $this->shopId)) {
                    $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_204));
                } else {
                    $msg = 'Unable to reset some configurations.';
                    $this->module->helper->logDebugMsg(__METHOD__, ['info' => $msg]);
                    $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_500, [], $msg));
                }
            }
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }
}