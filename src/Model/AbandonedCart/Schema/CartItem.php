<?php

namespace Apsis\One\Model\AbandonedCart\Schema;

use Apsis\One\Model\AbstractSchema;

class CartItem extends AbstractSchema
{
    /**
     * CartItem constructor.
     */
    public function __construct()
    {
        $this->definition = [
            self::KEY_MAIN => array_merge(
                self::SCHEMA_FIELD_GROUP_PRODUCT,
                self::SCHEMA_FIELD_PRODUCT_QTY,
                self::SCHEMA_FIELD_GROUP_PRODUCT_PRICE
            )
        ];
        $this->definitionTypes = [self::KEY_MAIN];
    }
}
