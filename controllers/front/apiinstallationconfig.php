<?php

use Apsis\One\Controller\AbstractApiController;
use Apsis\One\Model\EntityInterface as EI;
use Apsis\One\Model\Profile;
use Apsis\One\Model\SchemaInterface;
use Apsis\One\Module\SetupInterface;

class apsis_OneApiinstallationconfigModuleFrontController extends AbstractApiController
{
    /**
     * {@inheritdoc}
     */
    protected function initClassProperties(): void
    {
        $this->validRequestMethod = self::VERB_POST;
        $this->validBodyParams = [
            SetupInterface::INSTALLATION_CONFIG_CLIENT_ID => self::DATA_TYPE_STRING,
            SetupInterface::INSTALLATION_CONFIG_CLIENT_SECRET => self::DATA_TYPE_STRING,
            SetupInterface::INSTALLATION_CONFIG_SECTION_DISCRIMINATOR => self::DATA_TYPE_STRING,
            SetupInterface::INSTALLATION_CONFIG_KEYSPACE_DISCRIMINATOR => self::DATA_TYPE_STRING,
            SetupInterface::INSTALLATION_CONFIG_API_BASE_URL  => SchemaInterface::VALIDATE_FORMAT_URL_NOT_NULL
        ];
        $this->validQueryParams = [self::QUERY_PARAM_CONTEXT_IDS => self::DATA_TYPE_STRING];
        $this->optionalQueryParams = [self::QUERY_PARAM_RESET => self::DATA_TYPE_INT];
        $this->optionalQueryParamIgnoreRelations = [
            self::QUERY_PARAM_RESET => [self::PARAM_TYPE_BODY => [self::PARAM_TYPE_BODY]]
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function handleRequest(): void
    {
        try {
            $this->checkForResetParam();
            $this->saveInstallationConfigs();
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }

    /**
     * @return void
     */
    protected function saveInstallationConfigs(): void
    {
        try {
            $groupId = (int) $this->groupId;
            $shopId = (int) $this->shopId;

            $status = $this->configs->saveInstallationConfigs($this->bodyParams, $groupId, $shopId);
            if ($status) {
                $this->configs->saveProfileSyncFlag(SetupInterface::FLAG_YES, $groupId, $shopId);
                $this->configs->saveEventSyncFlag(SetupInterface::FLAG_YES, $groupId, $shopId);
                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_204));
            } else {
                $msg = 'Unable to save some configurations.';
                $this->module->helper->logDebugMsg(__METHOD__, ['info' => $msg]);
                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_500, [], $msg));
            }
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }

    /**
     * @return void
     */
    protected function checkForResetParam(): void
    {
        try {
            if (isset($this->queryParams[self::QUERY_PARAM_RESET])) {
                $repository = $this->getProfileRepository();

                /** @var Profile $entity */
                $entity = $repository->getNewEntity();
                $where = $repository->buildWhereClause(
                    [EI::C_ID_SHOP => $this->module->helper->getStoreIdArrFromContext(
                        (int) $this->groupId,
                        (int) $this->shopId)
                    ]
                );

                if ($this->configs->disableSyncsClearConfigs((int) $this->groupId, (int) $this->shopId) &&
                    $entity->resetProfilesAndEvents($where)
                ) {
                    $this->module->helper->logDebugMsg(__METHOD__, ['info' => 'Full reset performed.']);
                    $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_204));
                } else {
                    $msg = 'Unable to complete reset.';
                    $this->module->helper->logDebugMsg(__METHOD__, ['info' => $msg]);
                    $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_500, [], $msg));
                }
            }
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }
}
