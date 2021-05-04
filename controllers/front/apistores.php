<?php

use Apsis\One\Controller\AbstractApiController;

class apsis_OneApistoresModuleFrontController extends AbstractApiController
{
    /**
     * @var string
     */
    protected $validRequestMethod = AbstractApiController::HTTP_GET;

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
            $shopsList = $this->configurationRepository->getPrestaShopContext()->getAllContextList();
            $this->exitWithResponse($this->generateResponse(200, ['shops' => $shopsList]));
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }
}