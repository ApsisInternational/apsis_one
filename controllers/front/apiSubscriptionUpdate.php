<?php

use Apsis\One\Controller\AbstractApiController;

class apsis_OneApiSubscriptionUpdateModuleFrontController extends AbstractApiController
{
    const BODY_PARAM_PROFILE_KEY = 'PK';

    /**
     * @var string
     */
    protected $validRequestMethod = AbstractApiController::HTTP_PATCH;

    /**
     * @var array
     */
    protected $validBodyParams = [self::BODY_PARAM_PROFILE_KEY => AbstractApiController::DATA_TYPE_STRING];

    /**
     * @var array
     */
    protected $validQueryParams = [
        AbstractApiController::QUERY_PARAM_CONTEXT_IDS => AbstractApiController::DATA_TYPE_STRING
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
            $this->validateProfileSyncFeature();

            if ($profile = $this->getProfile() === false) {
                $msg = 'Profile not found.';
                $this->exitWithResponse($this->generateResponse(AbstractApiController::HTTP_CODE_404, [], $msg));
            }

            if ($this->updateSubscription($profile) === false) {
                $msg = 'Unable to update subscription for Profile.';
                $this->exitWithResponse($this->generateResponse(AbstractApiController::HTTP_CODE_500, [], $msg));
            }

            $this->exitWithResponse($this->generateResponse(AbstractApiController::HTTP_CODE_204));
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    /**
     * @return bool|object
     */
    private function getProfile()
    {
        try {
            //@toDo fetch profile
            return new stdClass();
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
            return false;
        }
    }

    /**
     * @param $profile
     *
     * @return bool
     */
    private function updateSubscription($profile)
    {
        try {
            //@toDo update subscription
            return true;
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
            return false;
        }
    }
}