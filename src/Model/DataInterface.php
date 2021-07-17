<?php

namespace Apsis\One\Model;

use Apsis\One\Helper\HelperInterface;
use Throwable;

interface DataInterface
{
    /**
     * Class constructor.
     *
     * @param HelperInterface $helper
     */
    public function __construct(HelperInterface $helper);

    /**
     * @param object $object
     * @param SchemaInterface $schema
     *
     * @return DataInterface
     *
     * @throws Throwable
     */
    public function setObject($object, SchemaInterface $schema): DataInterface;

    /**
     * @return array
     */
    public function getData(): array;
}