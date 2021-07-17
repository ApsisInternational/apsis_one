<?php

namespace Apsis\One\Grid\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

interface FilterInterface extends SearchCriteriaInterface
{
    /**
     * @return array
     */
    public static function getDefaults(): array;
}