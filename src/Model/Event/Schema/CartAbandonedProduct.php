<?php

namespace Apsis\One\Model\Event\Schema;

use Apsis\One\Model\AbstractSchema;

class CartAbandonedProduct extends AbstractSchema
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->definition = [
            self::KEY_MAIN => array_merge(
                self::SCHEMA_FIELD_CART_ID,
                self::SCHEMA_FIELD_GROUP_PRODUCT,
                self::SCHEMA_FIELD_PRODUCT_QTY,
                self::SCHEMA_FIELD_GROUP_PRODUCT_PRICE
            )
        ];
        $this->definitionTypes = [self::KEY_MAIN];
    }
}
