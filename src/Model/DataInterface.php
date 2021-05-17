<?php

namespace Apsis\One\Model;

use Exception;

interface DataInterface
{
    /**
     * @param object $object
     * @param SchemaInterface $schema
     *
     * @return DataInterface
     *
     * @throws Exception
     */
    public function setObject($object, SchemaInterface $schema): DataInterface;

    /**
     * @return array
     */
    public function getData(): array;
}