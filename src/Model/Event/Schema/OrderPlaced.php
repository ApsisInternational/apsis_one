<?php

namespace Apsis\One\Model\Event\Schema;

use Apsis\One\Model\AbstractSchema;
use Apsis\One\Helper\HelperInterface;

class OrderPlaced extends AbstractSchema
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->definition = [
            self::KEY_MAIN => array_merge(
                self::SCHEMA_FIELD_ORDER_ID,
                self::SCHEMA_FIELD_CART_ID,
                self::SCHEMA_FIELD_CUSTOMER_ID,
                self::SCHEMA_FIELD_GROUP_SHOP,
                self::SCHEMA_FIELD_CURRENCY_CODE,
                self::SCHEMA_FIELD_GROUP_SALES,
                self::SCHEMA_FIELD_GROUP_ORDER
            ),
            self::KEY_ITEMS => [
                self::KEY_SCHEMA => HelperInterface::SERVICE_EVENT_COMMON_ORDER_PLACED_PRODUCT_SCHEMA,
                self::KEY_CONTAINER => HelperInterface::SERVICE_EVENT_CONTAINER
            ]
        ];
        $this->definitionTypes = [self::KEY_MAIN, self::KEY_ITEMS];
    }
}
