<?php

namespace Apsis\One\Entity\Collection;

use Apsis\One\Entity\Event;
use PrestaShopCollection;
use PrestaShopException;

class EventCollection extends PrestaShopCollection implements CollectionInterface
{
    /**
     * EventCollection constructor.
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
        return Event::class;
    }
}