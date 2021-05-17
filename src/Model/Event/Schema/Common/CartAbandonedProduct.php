<?php

namespace Apsis\One\Model\Event\Schema\Common;

use Apsis\One\Model\AbstractSchema;

class CartAbandonedProduct extends AbstractSchema
{
    /**
     * CartAbandonedProduct constructor.
     */
    public function __construct()
    {
        $this->definition = [
            self::EVENT_COMMON_CART_ABANDONED_PRODUCT_DISCRIMINATOR => array_merge(
                self::SCHEMA_FIELD_CART_ID,
                self::SCHEMA_FIELD_GROUP_PRODUCT,
                self::SCHEMA_FIELD_PRODUCT_QTY,
                self::SCHEMA_FIELD_GROUP_PRODUCT_PRICE
            )
        ];
        $this->definitionTypes = [self::EVENT_COMMON_CART_ABANDONED_PRODUCT_DISCRIMINATOR];
    }
}