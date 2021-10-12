<?php

namespace Apsis\One\Grid\Data\Factory;

use Apsis\One\Model\EntityInterface as EI;

class ProfileGridDataFactoryDecorator extends CommonGridDataFactoryDecorator
{
    /**
     * {@inheritdoc}
     */
    protected function getColumns(): array
    {
        return array_merge(
            [EI::C_ID_CUSTOMER => self::NO_ID, EI::C_ID_NEWSLETTER => self::NO_ID],
            parent::getColumns()
        );
    }
}
