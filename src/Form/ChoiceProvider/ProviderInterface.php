<?php

namespace Apsis\One\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

interface ProviderInterface extends FormChoiceProviderInterface
{
    /**
     * @return array
     */
    public function getChoices(): array;
}