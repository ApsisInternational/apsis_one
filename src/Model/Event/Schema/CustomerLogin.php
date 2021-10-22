<?php

namespace Apsis\One\Model\Event\Schema;

use Apsis\One\Model\AbstractSchema;

class CustomerLogin extends AbstractSchema
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->definition = [
            self::KEY_MAIN => array_merge(
                self::SCHEMA_FIELD_CUSTOMER_ID,
                self::SCHEMA_FIELD_GROUP_SHOP
            )
        ];
        $this->definitionTypes = [self::KEY_MAIN];
    }
}
