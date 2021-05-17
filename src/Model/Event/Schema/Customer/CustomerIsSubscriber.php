<?php

namespace Apsis\One\Model\Event\Schema\Customer;

use Apsis\One\Model\AbstractSchema;

class CustomerIsSubscriber extends AbstractSchema
{
    /**
     * CustomerIsSubscriber constructor.
     */
    public function __construct()
    {
        $this->definition = [
            self::EVENT_CUSTOMER_IS_SUBSCRIBER_DISCRIMINATOR => array_merge(
                self::SCHEMA_FIELD_CUSTOMER_ID,
                self::SCHEMA_FIELD_GROUP_CONTEXT,
                self::SCHEMA_FIELD_IP_NEWSLETTER
            )
        ];
        $this->definitionTypes = [self::EVENT_CUSTOMER_IS_SUBSCRIBER_DISCRIMINATOR];
    }
}