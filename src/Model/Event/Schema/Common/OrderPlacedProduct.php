<?php

namespace Apsis\One\Model\Event\Schema\Common;

use Apsis\One\Model\AbstractSchema;

class OrderPlacedProduct extends AbstractSchema
{
    /**
     * OrderPlacedProduct constructor.
     */
    public function __construct()
    {
        $this->definition = [
            self::EVENT_COMMON_ORDER_PLACED_PRODUCT_DISCRIMINATOR => array_merge(
                self::SCHEMA_FIELD_GROUP_ORDER_PRODUCT,
                self::SCHEMA_FIELD_ORDER_ID,
                self::SCHEMA_FIELD_PRODUCT_QTY,
                self::SCHEMA_FIELD_GROUP_PRODUCT
            )
        ];
        $this->definitionTypes = [self::EVENT_COMMON_ORDER_PLACED_PRODUCT_DISCRIMINATOR];
    }
}