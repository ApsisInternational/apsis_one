<?php

use Apsis\One\Context\LinkContext;
use Apsis\One\Controller\ApiControllerInterface;
use Apsis\One\Helper\EntityHelper;
use Apsis\One\Helper\HelperInterface;
use Apsis\One\Model\AbandonedCart;
use Apsis\One\Module\Configuration\Configs;

class apsis_OneApicartrebuildModuleFrontController extends ModuleFrontController
{
    /**
     * @var Apsis_one
     */
    public $module;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        try {
            parent::__construct();
            $this->controller_type = 'module';
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            $this->redirectToHomepage(null, $e->getMessage(), __METHOD__);
        }
    }

    public function init()
    {
        try {
            /** @var EntityHelper $entityHelper */
            $entityHelper = $this->module->helper->getService(HelperInterface::SERVICE_HELPER_ENTITY);
            /** @var Configs $configs */
            $configs = $this->module->helper->getService(HelperInterface::SERVICE_MODULE_CONFIGS);

            if (empty($token = Tools::getValue(ApiControllerInterface::QUERY_PARAM_TOKEN))) {
                $msg = sprintf(
                    "Missing or invalid value for query param {%s}. ",
                    ApiControllerInterface::QUERY_PARAM_TOKEN
                );
                $this->redirectToHomepage(null, $msg, __METHOD__);
            }

            if (is_null($abandonedCart = $this->getAbandonedCart($entityHelper, $token)) ||
                ! Validate::isLoadedObject($abandonedCart)
            ) {
                $msg = sprintf("No abandoned cart found for token {%s}. ", $token);
                $this->redirectToHomepage(null, $msg, __METHOD__);
            }

            $shopId = $abandonedCart->getIdShop();

            if ($this->module->helper->isModuleEnabledForContext(null, $shopId) === false) {
                $msg = sprintf('Module is disabled. Shop Id {%d} Token {%s}. ', $shopId, $token);
                $this->redirectToHomepage($shopId, $msg, __METHOD__);
            }

            if (empty($configs->getInstallationConfigs(null, $shopId))) {
                $msg = sprintf('Module is not connected to any installation. Shop Id {%d} Token {%s}. ', $shopId, $token);
                $this->redirectToHomepage($shopId, $msg, __METHOD__);
            }

            $this->recoverCartAndRedirect($abandonedCart);
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            $this->redirectToHomepage(null, $e->getMessage(), __METHOD__);
        }
    }

    /**
     * @param EntityHelper $entityHelper
     * @param string $token
     *
     * @return AbandonedCart|null
     */
    private function getAbandonedCart(EntityHelper $entityHelper, string $token): ?AbandonedCart
    {
        try {
            if (strlen($token)) {
                return $entityHelper->getAbandonedCartRepository()->findOneByToken($token);
            }
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            $this->redirectToHomepage(null, $e->getMessage(), __METHOD__);
        }

        return null;
    }

    /**
     * @param int|null $shopId
     * @param string|null $msg
     * @param string|null $method
     */
    private function redirectToHomepage(?int $shopId = null, ?string $msg = null, ?string $method = null): void
    {
        if (strlen($msg) && strlen($method)) {
            $this->module->helper->logDebugMsg($method, ['info' => $msg . 'Redirected to homepage.']);
        }

        /** @var LinkContext $linkContext */
        $linkContext = $this->module->helper->getService(HelperInterface::SERVICE_CONTEXT_LINK);
        Tools::redirect($linkContext->getBaseUrl($shopId));
    }

    /**
     * @param AbandonedCart $abandonedCart
     */
    private function recoverCartAndRedirect(AbandonedCart $abandonedCart): void
    {
        if (Validate::isLoadedObject($cart = new Cart($abandonedCart->getIdCart())) &&
            Validate::isLoadedObject($customer = new Customer((int) $cart->id_customer))
        ) {
            $customer->logged = 1;
            $this->setInContext($customer, $cart->id);
        }

        $this->module->helper->logDebugMsg(
            __METHOD__,
            ['info' => sprintf('Successfully rebuild cart for given token {%s}.', $abandonedCart->getToken())]
        );

        Tools::redirect($this->context->link->getPageLink('cart', true, null, null, false, $cart->id_shop));
    }

    /**
     * @param Customer $customer
     *
     * @param int $cartId
     */
    private function setInContext(Customer $customer, int $cartId)
    {
        try {
            $this->context->customer = $customer;
            $this->context->cookie->id_customer = $customer->id;
            $this->context->cookie->logged = 1;
            $this->context->cookie->passwd = $customer->passwd;
            $this->context->cookie->check_cgv = 1;
            $this->context->cookie->customer_firstname = $customer->firstname;
            $this->context->cookie->customer_lastname = $customer->lastname;
            $this->context->cookie->is_guest = $customer->isGuest();
            $this->context->cookie->email = $customer->email;
            $this->context->cookie->id_cart = $cartId;
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            $this->redirectToHomepage(null, $e->getMessage(), __METHOD__);
        }
    }
}
