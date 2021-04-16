<?php

namespace Apsis\One\Context;

use ContextCore;

class PrestaShopContext
{
    /**
     * @var ContextCore
     */
    private $context;

    /**
     * PrestaShopContext constructor.
     */
    public function __construct()
    {
        $this->context = ContextCore::getContext();
    }

    /**
     * @param bool $null_value_without_multishop
     *
     * @return int
     */
    public function getCurrentShopId($null_value_without_multishop = false)
    {
        return (int) $this->context->shop->getContextShopID($null_value_without_multishop);
    }

    /**
     * @param bool $null_value_without_multishop
     *
     * @return int
     */
    public function getCurrentShopGroupId($null_value_without_multishop = false)
    {
        return (int) $this->context->shop->getContextShopGroupID($null_value_without_multishop);
    }
}
