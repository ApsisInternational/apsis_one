<?php

namespace Apsis\One\Grid\Search\Filters;

use Apsis\One\Grid\Definition\Factory\EventGridDefinitionFactory;

class EventFilters extends AbstractFilter
{
    const GRID_ID = EventGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    protected $filterId = self::GRID_ID;
}
