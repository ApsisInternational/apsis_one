<?php

namespace Apsis\One\Grid\Query;

use Apsis\One\Grid\Definition\Factory\EventGridDefinitionFactory;

class EventQueryBuilder extends AbstractQueryBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function getTableName(): string
    {
        return EventGridDefinitionFactory::GRID_ID;
    }
}