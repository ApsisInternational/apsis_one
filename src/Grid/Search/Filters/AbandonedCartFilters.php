<?php

namespace Apsis\One\Grid\Search\Filters;

use Apsis\One\Entity\EntityInterface as EI;
use Apsis\One\Grid\Definition\Factory\AbandonedCartGridDefinitionFactory;

class AbandonedCartFilters extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    protected $filterId = AbandonedCartGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    protected static function getOrderByColumn(): string
    {
        return EI::C_DATE_UPD;
    }
}
