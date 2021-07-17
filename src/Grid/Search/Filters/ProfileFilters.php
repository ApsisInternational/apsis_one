<?php

namespace Apsis\One\Grid\Search\Filters;

use Apsis\One\Entity\EntityInterface as EI;
use Apsis\One\Grid\Definition\Factory\ProfileGridDefinitionFactory;

class ProfileFilters extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    protected $filterId = ProfileGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    protected static function getOrderByColumn(): string
    {
        return EI::C_DATE_UPD;
    }
}
