<?php

namespace Apsis\One\Model\Event\Schema\Common;

use Apsis\One\Model\AbstractSchema;

class ProductReviewed extends AbstractSchema
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->definition = [
            self::EVENT_COMMON_PRODUCT_REVIEWED_DISCRIMINATOR => array_merge(
                self::SCHEMA_FIELD_COMMENT_ID,
                self::SCHEMA_FIELD_CUSTOMER_ID,
                self::SCHEMA_FIELD_GUEST_ID,
                self::SCHEMA_FIELD_GROUP_SHOP,
                self::SCHEMA_FIELD_GROUP_PRODUCT,
                self::SCHEMA_FIELD_GROUP_PRODUCT_PRICE,
                self::SCHEMA_FIELD_CURRENCY_CODE
            )
        ];
        $this->definitionTypes = [self::EVENT_COMMON_PRODUCT_REVIEWED_DISCRIMINATOR];
    }
}
