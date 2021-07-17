<?php

use Apsis\One\Controller\AbstractApiController;

class apsis_OneApisubscriptionupdateModuleFrontController extends AbstractApiController
{
    /**
     * {@inheritdoc}
     */
    protected function initClassProperties(): void
    {
        $this->validRequestMethod = self::VERB_PATCH;
        $this->validBodyParams = [self::BODY_PARAM_PROFILE_KEY => self::DATA_TYPE_STRING];
        $this->validQueryParams = [self::QUERY_PARAM_CONTEXT_IDS => self::DATA_TYPE_STRING];
    }

    /**
     * {@inheritdoc}
     */
    protected function handleRequest(): void
    {
        try {
            $this->validateProfileSyncFeature();

            if ($profile = $this->getProfile() === null) {
                $msg = 'Profile not found.';
                $this->module->helper->logDebugMsg(__METHOD__, ['info' => $msg]);

                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_404, [], $msg));
            }

            if ($this->updateSubscription($profile) === false) {
                $msg = 'Unable to update subscription for Profile.';
                $this->module->helper->logDebugMsg(__METHOD__, ['info' => $msg]);

                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_500, [], $msg));
            }

            $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_204));
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
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
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
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
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
            return false;
        }
    }
}