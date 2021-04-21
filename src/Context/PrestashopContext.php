<?php

namespace Apsis\One\Context;

use ContextCore;
use Link;

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

    /**
     * @return Link
     */
    public function getLink()
    {
        return $this->context->link;
    }

    /**
     * @return array
     */
    public function getAllContextList()
    {
        $result[] = ['context_ids' => '0,0', 'context_name' => 'All shops'];
        foreach ($this->context->shop->getShops(true, null, true) as $shopId) {
            $group = $this->context->shop->getGroupFromShop($shopId, false);
            if (! isset($result[$group['id']])) {
                $result[$group['id']] = [
                    'context_ids' => '0,' . $group['id'],
                    'context_name' => 'SHOP GROUP: ' . $group['name']
                ];
            } else {
                foreach ($group['shops'] as $shop) {
                    if ($shop['active']) {
                        $result[] = [
                            'context_ids' => $group['id'] . ',' . $shop['id_shop'],
                            'context_name' => '---- SHOP: ' . $shop['name']
                        ];
                    }
                }
            }
        }
        return array_values($result);
    }
}
