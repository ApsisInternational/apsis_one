<?php

namespace Apsis\One\Model\AbandonedCart\Schema;

use Apsis\One\Helper\HelperInterface;
use Apsis\One\Model\AbstractSchema;

class Cart extends AbstractSchema
{
    /**
     * CartAbandoned constructor.
     */
    public function __construct()
    {
        $this->definition = [
            self::KEY_MAIN => array_merge(
                self::SCHEMA_FIELD_CART_ID,
                self::SCHEMA_FIELD_CUSTOMER_ID,
                self::SCHEMA_FIELD_GUEST_ID,
                self::SCHEMA_FIELD_GROUP_SHOP,
                self::SCHEMA_FIELD_GROUP_SALES,
                self::SCHEMA_FIELD_GROUP_CART,
                self::SCHEMA_FIELD_CURRENCY_CODE
            ),
            self::KEY_ITEMS => [
                self::KEY_SCHEMA => HelperInterface::SERVICE_ABANDONED_CART_ITEM_SCHEMA,
                self::KEY_CONTAINER => HelperInterface::SERVICE_ABANDONED_CART_CONTAINER
            ]
        ];
        $this->definitionTypes = [self::KEY_MAIN, self::KEY_ITEMS];
    }
}
