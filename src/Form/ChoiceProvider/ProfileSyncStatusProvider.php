<?php

namespace Apsis\One\Form\ChoiceProvider;

use Apsis\One\Model\EntityInterface as EI;

class ProfileSyncStatusProvider extends AbstractProvider
{
    /**
     * {@inheritdoc}
     */
    protected function getOptions(): array
    {
        return EI::COLUMN_SS_LABEL_MAPPINGS;
    }
}
