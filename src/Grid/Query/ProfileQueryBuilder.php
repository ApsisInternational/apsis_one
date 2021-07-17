<?php

namespace Apsis\One\Grid\Query;

use Apsis\One\Grid\Definition\Factory\ProfileGridDefinitionFactory;

class ProfileQueryBuilder extends AbstractQueryBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function getTableName(): string
    {
        return ProfileGridDefinitionFactory::GRID_ID;
    }
}