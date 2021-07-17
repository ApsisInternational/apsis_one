<?php

namespace Apsis\One\Entity\Repository;

use Apsis\One\Entity\Event;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityRepository;

class EventRepository extends EntityRepository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getEntityClassName(): string
    {
        return Event::class;
    }
}