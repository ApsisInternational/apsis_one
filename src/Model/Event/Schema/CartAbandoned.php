<?php

namespace Apsis\One\Model\Event\Schema;

use Apsis\One\Model\AbstractSchema;
use Apsis\One\Helper\HelperInterface;

class CartAbandoned extends AbstractSchema
{
    /**
     * {@inheritdoc}
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
                self::SCHEMA_FIELD_CURRENCY_CODE
            ),
            self::KEY_ITEMS => [
                self::KEY_SCHEMA => HelperInterface::SERVICE_EVENT_COMMON_CART_ABANDONED_PRODUCT_SCHEMA
            ]
        ];
        $this->definitionTypes = [self::KEY_MAIN, self::KEY_ITEMS];
    }
}
