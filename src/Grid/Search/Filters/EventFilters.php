<?php

namespace Apsis\One\Grid\Search\Filters;

use Apsis\One\Model\EntityInterface as EI;
use Apsis\One\Grid\Definition\Factory\EventGridDefinitionFactory;

class EventFilters extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    protected $filterId = EventGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    protected static function getOrderByColumn(): string
    {
        return EI::C_DATE_ADD;
    }
}
