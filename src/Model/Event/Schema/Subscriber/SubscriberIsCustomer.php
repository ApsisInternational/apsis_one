<?php

namespace Apsis\One\Model\Event\Schema\Subscriber;

use Apsis\One\Model\AbstractSchema;

class SubscriberIsCustomer extends AbstractSchema
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->definition = [
            self::EVENT_SUBSCRIBER_IS_CUSTOMER_DISCRIMINATOR => array_merge(
                self::SCHEMA_FIELD_CUSTOMER_ID,
                self::SCHEMA_FIELD_GROUP_SHOP
            )
        ];
        $this->definitionTypes = [self::EVENT_SUBSCRIBER_IS_CUSTOMER_DISCRIMINATOR];
    }
}
