<?php

namespace Apsis\One\Form\ChoiceProvider;

use Apsis\One\Entity\EntityInterface as EI;

class EventTypeProvider extends AbstractProvider
{
    /**
     * {@inheritdoc}
     */
    protected function getOptions(): array
    {
        return EI::COLUMN_ET_LABEL_MAPPINGS;
    }
}
