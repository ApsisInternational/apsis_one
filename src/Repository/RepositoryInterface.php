<?php

namespace Apsis\One\Repository;

interface RepositoryInterface
{
    /**
     * @param int $id
     *
     * @return object|null
     */
    public function getById(int $id);
}