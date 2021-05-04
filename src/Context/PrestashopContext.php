<?php

namespace Apsis\One\Context;

use Apsis\One\Helper\LoggerHelper;
use ContextCore;
use Link;
use Exception;
use PrestaShopException;
use Shop;

class PrestashopContext
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
            foreach ($this->getAllActiveShopIdsAsList() as $shopId) {
                $group = $this->context->shop->getGroupFromShop($shopId, false);
                $gIndex = 'g' . (int) $group['id'];
                if (! isset($result[$gIndex])) {
                    $result[$gIndex] = [
                        'context_ids' => '0,' . $group['id'],
                        'context_name' => 'SHOP GROUP: ' . $group['name']
                    ];

                    foreach ($group['shops'] as $shop) {
                        if ($shop['active']) {
                            $result[] = [
                                'context_ids' => $group['id'] . ',' . $shop['id_shop'],
                                'context_name' => 'SHOP: ' . $shop['name']
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

    public function getShopListGroupedByGroup()
    {
        try {
            $result = [];
            foreach ($this->getAllActiveShopIdsAsList() as $shopId) {
                $groupId = $this->context->shop->getGroupFromShop($shopId, true);
                $result[$groupId][] = $shopId;
            }
            return $result;
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return [];
        }
    }

    /**
     * @return array
     */
    public function getAllActiveShopIdsAsList()
    {
        try {
            return $this->context->shop->getShops(true, null, true);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return [];
        }
    }

    /**
     * @param null $idShopGroup
     * @param null $idShop
     *
     * @throws PrestaShopException
     */
    public function setContext($idShopGroup = null, $idShop = null)
    {
        if ($idShop) {
            $this->context->shop->setContext(Shop::CONTEXT_SHOP, $idShop);
        } elseif ($idShopGroup) {
            $this->context->shop->setContext(Shop::CONTEXT_GROUP, $idShopGroup);
        } else {
            $this->context->shop->setContext(Shop::CONTEXT_ALL);
        }
    }
}
