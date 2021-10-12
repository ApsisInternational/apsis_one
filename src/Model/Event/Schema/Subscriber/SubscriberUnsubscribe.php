<?php

namespace Apsis\One\Model\Event\Schema\Subscriber;

use Apsis\One\Model\AbstractSchema;

class SubscriberUnsubscribe extends AbstractSchema
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->definition = [
            self::EVENT_SUBSCRIBER_UNSUBSCRIBE_DISCRIMINATOR => array_merge(
                self::SCHEMA_FIELD_CUSTOMER_ID,
                self::SCHEMA_FIELD_GUEST_ID,
                self::SCHEMA_FIELD_SUBSCRIBER_ID,
                self::SCHEMA_FIELD_GROUP_SHOP,
                self::SCHEMA_FIELD_IP_NEWSLETTER
            )
        ];
        $this->definitionTypes = [self::EVENT_SUBSCRIBER_UNSUBSCRIBE_DISCRIMINATOR];
    }
}
