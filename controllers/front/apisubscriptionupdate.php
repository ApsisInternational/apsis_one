<?php

use Apsis\One\Controller\AbstractApiController;

class apsis_OneApisubscriptionupdateModuleFrontController extends AbstractApiController
{
    /**
     * @inheritdoc
     */
    protected $validRequestMethod = self::VERB_PATCH;

    /**
     * @inheritdoc
     */
    protected $validBodyParams = [self::BODY_PARAM_PROFILE_KEY => self::DATA_TYPE_STRING];

    /**
     * @inheritdoc
     */
    protected $validQueryParams = [self::QUERY_PARAM_CONTEXT_IDS => self::DATA_TYPE_STRING];

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
            $this->validateProfileSyncFeature();

            if ($profile = $this->getProfile() === null) {
                $msg = 'Profile not found.';
                $this->module->helper->logErrorMessage(__METHOD__, $msg);

                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_404, [], $msg));
            }

            if ($this->updateSubscription($profile) === false) {
                $msg = 'Unable to update subscription for Profile.';
                $this->module->helper->logErrorMessage(__METHOD__, $msg);

                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_500, [], $msg));
            }

            $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_204));
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    /**
     * @return stdClass|null
     */
    protected function getProfile(): ?stdClass
    {
        try {
            // TODO: fetch profile from profile entity
            return new stdClass();
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
            return null;
        }
    }

    /**
     * @param $profile
     *
     * @return bool
     */
    protected function updateSubscription($profile): bool
    {
        try {
            // TODO: update subscription in profile entity and ps subscription entity
            return true;
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
            return false;
        }
    }
}