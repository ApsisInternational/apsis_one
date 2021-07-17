<?php

namespace Apsis\One\Entity\Repository;

interface RepositoryInterface
{
    /**
     * @return string
     */
    public static function getEntityClassName(): string;
}