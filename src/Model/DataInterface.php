<?php

namespace Apsis\One\Model;

use Apsis\One\Helper\HelperInterface;
use Throwable;

interface DataInterface
{
    const KEY_SALES = 'sales_columns';
    const KEY_ADD_COL = 'address_collection';
    const KEY_ADD_IDS = 'order_address_ids';

    const ADD_TYPE_BILLING = 1;
    const ADD_TYPE_SHIPPING = 2;
    const ADD_TYPE_MAP = [
        self::ADD_TYPE_BILLING => 'id_address_invoice',
        self::ADD_TYPE_SHIPPING => 'id_address_delivery'
    ];

    /**
     * Class constructor.
     *
     * @param HelperInterface $helper
     */
    public function __construct(HelperInterface $helper);

    /**
     * @param array $objectDataArr
     * @param SchemaInterface $schema
     *
     * @return DataInterface
     *
     * @throws Throwable
     */
    public function setObjectData(array $objectDataArr, SchemaInterface $schema): DataInterface;

    /**
     * @return array
     */
    public function getDataArr(): array;

    /**
     * @return string
     */
    public function toJson(): string;
}
