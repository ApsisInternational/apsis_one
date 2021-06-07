<?php

namespace Apsis\One\Context;

use Apsis\One\Module\SetupInterface;
use Link;
use Exception;

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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
            return '';
        }
    }
}
