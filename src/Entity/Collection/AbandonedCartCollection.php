<?php

namespace Apsis\One\Entity\Collection;

use Apsis\One\Entity\AbandonedCart;
use PrestaShopCollection;
use PrestaShopException;

class AbandonedCartCollection extends PrestaShopCollection implements CollectionInterface
{
    /**
     * AbandonedCartCollection constructor.
     *
     * @param string $classname
     *
     * @throws PrestaShopException
     */
    public function __construct($classname = self::class)
    {
        parent::__construct($classname);
    }

    /**
     * {@inheritdoc}
     */
    public static function getEntityClassName(): string
    {
        return AbandonedCart::class;
    }
}