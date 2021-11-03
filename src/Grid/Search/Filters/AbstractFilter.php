<?php

namespace Apsis\One\Grid\Search\Filters;

use Apsis\One\Model\EntityInterface as EI;
use PrestaShop\PrestaShop\Core\Search\Filters;

abstract class AbstractFilter extends Filters implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => EI::T_PRIMARY_MAPPINGS[static::GRID_ID],
            'sortOrder' => 'DESC',
            'filters' => [],
        ];
    }
}
