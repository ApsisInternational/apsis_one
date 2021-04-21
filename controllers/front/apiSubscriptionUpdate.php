<?php

use Apsis\One\Controller\AbstractApiController;

class apsis_OneApiSubscriptionUpdateModuleFrontController extends AbstractApiController
{
    /**
     * @var string
     */
    protected $validRequestMethod = 'PATCH';

    /**
     * @var string[]
     */
    protected $validBodyParams = [
        'PK'
    ];

    public function init()
    {
        parent::init();
        // Do your thing
        $this->exitWithResponse($this->generateResponse(200));
    }
}