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
        try {
            if (Validate::isLoadedObject($cart = new Cart($abandonedCart->getIdCart()))) {
                $_POST['recover_cart'] = $cart->id;
                $_POST['token_cart'] = md5(_COOKIE_KEY_ . 'recover_cart_' . $cart->id);

                $this->module->helper->logInfoMsg(
                    sprintf('Successfully rebuild cart for given token {%s}.', $abandonedCart->getToken())
                );
                parent::init();
                Tools::redirect($this->context->link->getPageLink('cart', true, null, null, false, $cart->id_shop));
            } else {
                $this->module->helper->logInfoMsg(
                    sprintf('Unable to rebuild cart for given token {%s}.', $abandonedCart->getToken())
                );
                $this->redirectToHomepage(null, $e->getMessage(), __METHOD__);
            }
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            $this->redirectToHomepage(null, $e->getMessage(), __METHOD__);
        }
    }
}
