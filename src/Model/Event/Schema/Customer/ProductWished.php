<?php

namespace Apsis\One\Model\Event\Schema\Customer;

use Apsis\One\Model\AbstractSchema;

class ProductWished extends AbstractSchema
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->definition = [
            self::EVENT_CUSTOMER_PRODUCT_WISHED_DISCRIMINATOR => array_merge(
                self::SCHEMA_FIELD_GROUP_PRODUCT_WISHED,
                self::SCHEMA_FIELD_CUSTOMER_ID,
                self::SCHEMA_FIELD_GROUP_CONTEXT,
                self::SCHEMA_FIELD_GROUP_PRODUCT,
                self::SCHEMA_FIELD_PRODUCT_QTY,
                self::SCHEMA_FIELD_GROUP_PRODUCT_PRICE,
                self::SCHEMA_FIELD_CURRENCY_CODE
            )
        ];
        $this->definitionTypes = [self::EVENT_CUSTOMER_PRODUCT_WISHED_DISCRIMINATOR];
    }
}