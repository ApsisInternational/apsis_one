<?php

namespace Apsis\One\Grid\Data\Factory;

use Apsis\One\Entity\EntityInterface as EI;

class CommonGridDataFactoryDecorator extends AbstractGridDataFactoryDecorator
{
    /**
     * {@inheritdoc}
     */
    protected function getColumns(): array
    {
        return [EI::C_SYNC_STATUS => array_flip($this->choiceProvider->getChoices())];
    }
}