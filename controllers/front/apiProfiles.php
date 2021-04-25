<?php

use Apsis\One\Controller\AbstractApiController;

class apsis_OneApiProfilesModuleFrontController extends AbstractApiController
{
    /**
     * @var string
     */
    protected $validRequestMethod = AbstractApiController::HTTP_GET;

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