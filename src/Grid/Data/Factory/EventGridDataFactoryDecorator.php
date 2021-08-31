<?php

namespace Apsis\One\Grid\Data\Factory;

use Apsis\One\Entity\EntityInterface as EI;

class EventGridDataFactoryDecorator extends CommonGridDataFactoryDecorator
{
    /**
     * {@inheritdoc}
     */
    protected function getColumns(): array
    {
        return array_merge(
            [EI::C_EVENT_TYPE => array_flip($this->eventTypeProvider->getChoices())],
            parent::getColumns()
        );
    }
}
