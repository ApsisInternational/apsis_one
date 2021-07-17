<?php

namespace Apsis\One\Grid\Search\Filters;

use PrestaShop\PrestaShop\Core\Search\Filters;

abstract class AbstractFilter extends Filters implements FilterInterface
{
    /**
     * @return string
     */
    abstract static protected function getOrderByColumn(): string;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => static::getOrderByColumn(),
            'sortOrder' => 'DESC',
            'filters' => [],
        ];
    }
}