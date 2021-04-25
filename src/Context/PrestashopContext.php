<?php

namespace Apsis\One\Context;

use Apsis\One\Helper\LoggerHelper;
use ContextCore;
use Link;
use Exception;

class PrestaShopContext
{
    /**
     * @var ContextCore
     */
    protected $context;

    /**
     * @var LoggerHelper
     */
    protected $loggerHelper;

    /**
     * PrestaShopContext constructor.
     */
    public function __construct(LoggerHelper $loggerHelper)
    {
        $this->loggerHelper = $loggerHelper;
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
        try {
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
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return [];
        }
    }
}
