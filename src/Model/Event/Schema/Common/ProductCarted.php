<?php

namespace Apsis\One\Model\Event\Schema\Common;

use Apsis\One\Model\AbstractSchema;

class ProductCarted extends AbstractSchema
{
    /**
     * ProductCarted constructor.
     */
    public function __construct()
    {
        $this->definition = [
            self::EVENT_COMMON_PRODUCT_CARTED_DISCRIMINATOR => array_merge(
                self::SCHEMA_FIELD_CART_ID,
                self::SCHEMA_FIELD_CUSTOMER_ID,
                self::SCHEMA_FIELD_GUEST_ID,
                self::SCHEMA_FIELD_GROUP_CONTEXT,
                self::SCHEMA_FIELD_GROUP_PRODUCT,
                self::SCHEMA_FIELD_PRODUCT_QTY,
                self::SCHEMA_FIELD_GROUP_PRODUCT_PRICE,
                self::SCHEMA_FIELD_CURRENCY_CODE
            )
        ];
        $this->definitionTypes = [self::EVENT_COMMON_PRODUCT_CARTED_DISCRIMINATOR];
    }
}