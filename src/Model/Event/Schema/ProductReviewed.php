<?php

namespace Apsis\One\Model\Event\Schema;

use Apsis\One\Model\AbstractSchema;

class ProductReviewed extends AbstractSchema
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->definition = [
            self::KEY_MAIN => array_merge(
                self::SCHEMA_FIELD_GROUP_REVIEW,
                self::SCHEMA_FIELD_CUSTOMER_ID,
                self::SCHEMA_FIELD_GUEST_ID,
                self::SCHEMA_FIELD_GROUP_SHOP,
                self::SCHEMA_FIELD_GROUP_PRODUCT,
                self::SCHEMA_FIELD_GROUP_PRODUCT_PRICE,
                self::SCHEMA_FIELD_CURRENCY_CODE
            )
        ];
        $this->definitionTypes = [self::KEY_MAIN];
    }
}
