<?php

namespace Apsis\One\Model\AbandonedCart;

use Apsis\One\Model\Event\DataProvider as EventDataProvider;

class DataProvider extends EventDataProvider
{
    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getTotalProductsInclTax(string $type): ?float
    {
        return $this->getFormattedValueByType('total_product_incl_tax', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getTotalProductsExclTax(string $type): ?float
    {
        return $this->getFormattedValueByType('total_product_excl_tax', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getCartToken(string $type): ?string
    {
        return $this->getFormattedValueByType('token', $type);
    }

    /**
     * @param string $type
     *
     * @return int|null
     */
    protected function getItemsCount(string $type): ?int
    {
        return $this->getFormattedValueByType('items_count', $type);
    }
}
