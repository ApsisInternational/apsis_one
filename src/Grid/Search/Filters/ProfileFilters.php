<?php

namespace Apsis\One\Grid\Search\Filters;

use Apsis\One\Grid\Definition\Factory\ProfileGridDefinitionFactory;

class ProfileFilters extends AbstractFilter
{
    const GRID_ID = ProfileGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    protected $filterId = self::GRID_ID;
}
