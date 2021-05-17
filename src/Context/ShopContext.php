<?php

namespace Apsis\One\Context;

use Exception;
use PrestaShopException;
use Shop;

class ShopContext extends AbstractContext
{
    /**
     * @return AbstractContext
     */
    protected function setContextObject(): AbstractContext
    {
        $this->contextObject = $this->context->shop;
        return $this;
    }

    /**
     * @return Shop
     */
    public function getContextObject(): Shop
    {
        return $this->contextObject;
    }

    /**
     * @param bool $null_value_without_multishop
     *
     * @return int
     */
    public function getCurrentShopId(bool $null_value_without_multishop = false): int
    {
        return (int) $this->getContextObject()->getContextShopID($null_value_without_multishop);
    }

    /**
     * @param bool $null_value_without_multishop
     *
     * @return int
     */
    public function getCurrentShopGroupId(bool $null_value_without_multishop = false): int
    {
        return (int) $this->getContextObject()->getContextShopGroupID($null_value_without_multishop);
    }

    /**
     * @return array
     */
    public function getAllContextList(): array
    {
        try {
            $result[] = ['context_ids' => '0,0', 'context_name' => 'ALL SHOPS'];
            foreach ($this->getAllActiveShopIdsAsList() as $shopId) {
                $group = $this->getContextObject()->getGroupFromShop($shopId, false);
                $gIndex = 'g' . (int) $group['id'];
                if (! isset($result[$gIndex])) {
                    $result[$gIndex] = [
                        'context_ids' => $group['id'] . ',0',
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
            $this->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return [];
        }
    }

    /**
     * @return array
     */
    public function getShopListGroupedByGroup(): array
    {
        try {
            $result = [];
            foreach ($this->getAllActiveShopIdsAsList() as $shopId) {
                $groupId = $this->getContextObject()->getGroupFromShop($shopId, true);
                $result[$groupId][] = $shopId;
            }
            return $result;
        } catch (Exception $e) {
            $this->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return [];
        }
    }

    /**
     * @return array
     */
    public function getAllActiveShopIdsAsList(): array
    {
        try {
            return $this->getContextObject()->getShops(true, null, true);
        } catch (Exception $e) {
            $this->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return [];
        }
    }

    /**
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return void
     *
     * @throws PrestaShopException
     */
    public function setContext(?int $idShopGroup = null, ?int $idShop = null): void
    {
        if ($idShop) {
            $this->getContextObject()->setContext(Shop::CONTEXT_SHOP, $idShop);
        } elseif ($idShopGroup) {
            $this->getContextObject()->setContext(Shop::CONTEXT_GROUP, $idShopGroup);
        } else {
            $this->getContextObject()->setContext(Shop::CONTEXT_ALL);
        }
    }

    /**
     * @return bool
     */
    public function isMultiShopFeatureActive(): bool
    {
        try {
            return (bool) $this->getContextObject()->isFeatureActive();
        } catch (Exception $e) {
            $this->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }
}
