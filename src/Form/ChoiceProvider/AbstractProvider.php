<?php

namespace Apsis\One\Form\ChoiceProvider;

abstract class AbstractProvider implements ProviderInterface
{
    /**
     * @return array
     */
    abstract protected function getOptions(): array;

    /**
     * {@inheritdoc}
     */
    public function getChoices(): array
    {
        $choices = [];
        foreach (static::getOptions() as $value => $label) {
            $choices[$label] = $value;
        }
        return $choices;
    }
}