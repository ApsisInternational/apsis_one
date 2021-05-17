<?php

namespace Apsis\One\Model\AbandonedCart\Schema;

use Apsis\One\Helper\HelperInterface;
use Apsis\One\Model\AbstractSchema;

class CartItem extends AbstractSchema
{
    /**
     * CartItem constructor.
     */
    public function __construct()
    {
        $this->definition = [
            HelperInterface::SERVICE_ABANDONED_CART_ITEM_SCHEMA => array_merge(
                self::SCHEMA_FIELD_CART_ID,
                self::SCHEMA_FIELD_GROUP_PRODUCT,
                self::SCHEMA_FIELD_PRODUCT_QTY,
                self::SCHEMA_FIELD_GROUP_PRODUCT_PRICE
            )
        ];
        $this->definitionTypes = [HelperInterface::SERVICE_ABANDONED_CART_ITEM_SCHEMA];
    }
}