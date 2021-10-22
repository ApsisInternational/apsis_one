<?php

namespace Apsis\One\Model\Event\Schema;

use Apsis\One\Model\AbstractSchema;

class OrderPlacedProduct extends AbstractSchema
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->definition = [
            self::KEY_MAIN => array_merge(
                self::SCHEMA_FIELD_GROUP_ORDER_PRODUCT,
                self::SCHEMA_FIELD_ORDER_ID,
                self::SCHEMA_FIELD_PRODUCT_QTY,
                self::SCHEMA_FIELD_GROUP_PRODUCT
            )
        ];
        $this->definitionTypes = [self::KEY_MAIN];
    }
}
