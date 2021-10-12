<?php

namespace Apsis\One\Model\Event\Schema\Guest;

use Apsis\One\Model\AbstractSchema;

class GuestIsCustomer extends AbstractSchema
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->definition = [
            self::EVENT_GUEST_IS_CUSTOMER_DISCRIMINATOR => array_merge(
                self::SCHEMA_FIELD_GUEST_ID,
                self::SCHEMA_FIELD_CUSTOMER_ID,
                self::SCHEMA_FIELD_GROUP_SHOP
            )
        ];
        $this->definitionTypes = [self::EVENT_GUEST_IS_CUSTOMER_DISCRIMINATOR];
    }
}
