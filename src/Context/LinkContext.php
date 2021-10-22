<?php

namespace Apsis\One\Context;

use Apsis\One\Module\SetupInterface;
use Link;
use Product;
use Throwable;

class LinkContext extends AbstractContext
{
    /**
     * @return AbstractContext
     */
    protected function setContextObject(): AbstractContext
    {
        $this->contextObject = $this->context->link;
        return $this;
    }

    /**
     * @return Link
     */
    public function getContextObject(): Link
    {
        return $this->contextObject;
    }

    /**
     * @param string $controller
     * @param array $params
     * @param bool|null $ssl
     * @param int|null $idLang
     * @param int|null $idShop
     *
     * @return string
     */
    public function getModuleLink(
        string $controller,
        array $params = [],
        ?bool $ssl = null,
        ?int $idLang = null,
        ?int $idShop = null
    ): string {
        try {
            return (string) $this->getContextObject()
                ->getModuleLink(SetupInterface::MODULE_NAME, $controller, $params, $ssl, $idLang, $idShop);
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
            return '';
        }
    }

    /**
     * @param int|null $idShop
     * @param bool|null $ssl
     * @param false $relativeProtocol
     *
     * @return string
     */
    public function getBaseUrl(?int $idShop = null, ?bool $ssl = null, bool $relativeProtocol = false): string
    {
        try {
            return $this->getContextObject()->getBaseLink($idShop, $ssl, $relativeProtocol);
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
            return '';
        }
    }

    /**
     * @param Product $product
     *
     * @return string|null
     */
    public function getProductCoverImage(Product $product): ?string
    {
        try {
            $imgArr = $product->getCover($product->id);
            return ! empty($imgArr['id_image']) ?
                $this->getContextObject()->getImageLink($product->link_rewrite, $imgArr['id_image']) : null;
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }
}
