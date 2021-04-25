<?php

use Apsis\One\Controller\AbstractApiController;

class apsis_OneApiStoresModuleFrontController extends AbstractApiController
{
    /**
     * @var string
     */
    protected $validRequestMethod = AbstractApiController::HTTP_GET;

    public function init()
    {
        parent::init();

        $this->processRequest();
    }

    private function processRequest()
    {
        $shopsList = $this->configurationRepository->getPrestaShopContext()->getAllContextList();
        $this->exitWithResponse($this->generateResponse(200, $shopsList));
    }
}