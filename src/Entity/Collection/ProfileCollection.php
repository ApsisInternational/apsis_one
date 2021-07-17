<?php

namespace Apsis\One\Entity\Collection;

use Apsis\One\Entity\Profile;
use PrestaShopCollection;
use PrestaShopException;

class ProfileCollection extends PrestaShopCollection implements CollectionInterface
{
    /**
     * ProfileCollection constructor.
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
        return Profile::class;
    }
}