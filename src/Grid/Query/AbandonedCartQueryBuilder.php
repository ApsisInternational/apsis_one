<?php

namespace Apsis\One\Grid\Query;

use Apsis\One\Grid\Definition\Factory\AbandonedCartGridDefinitionFactory;

class AbandonedCartQueryBuilder extends AbstractQueryBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function getTableName(): string
    {
        return AbandonedCartGridDefinitionFactory::GRID_ID;
    }
}