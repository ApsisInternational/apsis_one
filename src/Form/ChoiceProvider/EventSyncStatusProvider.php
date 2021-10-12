<?php

namespace Apsis\One\Form\ChoiceProvider;

use Apsis\One\Model\EntityInterface as EI;

class EventSyncStatusProvider extends AbstractProvider
{
    /**
     * {@inheritdoc}
     */
    protected function getOptions(): array
    {
        $options = EI::COLUMN_SS_LABEL_MAPPINGS;
        unset($options[EI::SS_JUSTIN]);
        return $options;
    }
}
