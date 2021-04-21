<?php

use Apsis\One\Controller\AbstractApiController;

class apsis_OneApiProfilesModuleFrontController extends AbstractApiController
{
    /**
     * @var string
     */
    protected $validRequestMethod = 'GET';

    public function init()
    {
        parent::init();
        // Do your thing
        $this->exitWithResponse($this->generateResponse(200));
    }
}