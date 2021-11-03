<?php

namespace Apsis\One\Grid\Search\Filters;

use Apsis\One\Grid\Definition\Factory\AbandonedCartGridDefinitionFactory;

class AbandonedCartFilters extends AbstractFilter
{
    const GRID_ID = AbandonedCartGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    protected $filterId = self::GRID_ID;
}
