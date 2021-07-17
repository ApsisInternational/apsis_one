<?php

namespace Apsis\One\Entity\Repository;

use Apsis\One\Entity\Profile;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityRepository;

class ProfileRepository extends EntityRepository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getEntityClassName(): string
    {
        return Profile::class;
    }
}