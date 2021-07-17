<?php

use Apsis\One\Controller\AbstractApiController;
use Apsis\One\Helper\HelperInterface;
use Apsis\One\Context\ShopContext;

class apsis_OneApistoresModuleFrontController extends AbstractApiController
{
    /**
     * {@inheritdoc}
     */
    protected function initClassProperties(): void
    {
        $this->validRequestMethod = self::VERB_GET;
    }

    /**
     * {@inheritdoc}
     */
    protected function handleRequest(): void
    {
        try {
            /** @var ShopContext $shopContext */
            $shopContext = $this->module->helper->getService(HelperInterface::SERVICE_CONTEXT_SHOP);
            $this->exitWithResponse($this->generateResponse(200, ['shops' => $shopContext->getAllContextList()]));
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }
}