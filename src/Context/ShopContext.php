<?php

namespace Apsis\One\Context;

use ShopGroup;
use Throwable;
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
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
            return [];
        }
    }

    /**
     * @param int $shopId
     *
     * @return int|null
     */
    public function getGroupIdFromShopId(int $shopId): ?int
    {
        try {
            if (is_numeric($groupId = $this->getContextObject()->getGroupFromShop($shopId, true))) {
                return (int) $groupId;
            }
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
        }
        return null;
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
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
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
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
            return [];
        }
    }

    /**
     * @return array
     */
    public function getAllActiveShopsList(): array
    {
        try {
            return $this->getContextObject()->getShops();
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
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
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @param int|null $idShop
     *
     * @return Shop|null
     */
    public function getShopById(?int $idShop = null): ?Shop
    {
        try {
            if (! $idShop) {
                $idShop = $this->getCurrentShopId();
            }

            if ($idShop) {
                return new Shop($idShop);
            }
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
        }

        return null;
    }

    /**
     * @param int|null $idShop
     *
     * @return string|null
     */
    public function getShopNameById(?int $idShop = null): ?string
    {
        try {
            if ($shop = $this->getShopById($idShop)) {
                return $shop->name;
            }
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
        }

        return null;
    }

    /**
     * @param int|null $idShopGroup
     *
     * @return ShopGroup|null
     */
    public function getShopGroupById(?int $idShopGroup = null): ?ShopGroup
    {
        try {
            if (! $idShopGroup) {
                $idShopGroup = $this->getCurrentShopGroupId();
            }

            if ($idShopGroup) {
                return new ShopGroup($idShopGroup);
            }
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
        }

        return null;
    }

    /**
     * @param int|null $idShopGroup
     *
     * @return string|null
     */
    public function getShopGroupNameById(?int $idShopGroup = null): ?string
    {
        try {
            if ($shopGroup = $this->getShopGroupById($idShopGroup)) {
                return (string) $shopGroup->name;
            }
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
        }

        return null;
    }
}
