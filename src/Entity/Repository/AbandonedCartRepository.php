<?php

namespace Apsis\One\Entity\Repository;

use Apsis\One\Entity\AbandonedCart;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityRepository;

class AbandonedCartRepository extends EntityRepository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getEntityClassName(): string
    {
        return AbandonedCart::class;
    }
}