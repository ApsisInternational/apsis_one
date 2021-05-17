<?php

use Apsis\One\Controller\AbstractApiController;
use Apsis\One\Helper\HelperInterface;
use Apsis\One\Context\ShopContext;

class apsis_OneApistoresModuleFrontController extends AbstractApiController
{
    /**
     * @inheritdoc
     */
    protected $validRequestMethod = self::VERB_GET;

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
            /** @var ShopContext $shopContext */
            $shopContext = $this->module->helper->getService(HelperInterface::SERVICE_CONTEXT_SHOP);
            $this->exitWithResponse($this->generateResponse(200, ['shops' => $shopContext->getAllContextList()]));
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }
}