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
        parent::init();
        // Do your thing
        $this->exitWithResponse($this->generateResponse(200));
    }
}