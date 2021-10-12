<?php

namespace Apsis\One\Model;

abstract class AbstractDataProvider extends AbstractData
{
    /**
     * @param string $type
     *
     * @return int|null
     */
    protected function getCustomerId(string $type): ?int
    {
        return $this->getFormattedValueByType('id_customer', $type);
    }

    /**
     * @param string $type
     *
     * @return int|null
     */
    protected function getSubscriberId(string $type): ?int
    {
        return $this->getFormattedValueByType('id_subscriber', $type);
    }

    /**
     * @param string $type
     *
     * @return int|null
     */
    protected function getShopId(string $type): ?int
    {
        return $this->getFormattedValueByType('id_shop', $type);
    }

    /**
     * @return int
     */
    protected function getShopGroupId(string $type): ?int
    {
        return $this->getFormattedValueByType('id_shop_group', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getShopName(string $type): ?string
    {
        return $this->getFormattedValueByType('shop_name', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getShopGroupName(string $type): ?string
    {
        return $this->getFormattedValueByType('shop_group_name', $type);
    }

    /**
     * @param string $type
     *
     * @return bool|null
     */
    protected function getIsGuest(string $type): ?bool
    {
        return $this->getFormattedValueByType('is_guest', $type);
    }
}
