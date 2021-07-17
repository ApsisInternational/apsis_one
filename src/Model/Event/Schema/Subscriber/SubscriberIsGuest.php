<?php

namespace Apsis\One\Model\Event\Schema\Subscriber;

use Apsis\One\Model\AbstractSchema;

class SubscriberIsGuest extends AbstractSchema
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->definition = [
            self::EVENT_SUBSCRIBER_IS_GUEST_DISCRIMINATOR => array_merge(
                self::SCHEMA_FIELD_GUEST_ID,
                self::SCHEMA_FIELD_CUSTOMER_ID,
                self::SCHEMA_FIELD_GROUP_CONTEXT,
                self::SCHEMA_FIELD_IP_NEWSLETTER
            )
        ];
        $this->definitionTypes = [self::EVENT_SUBSCRIBER_IS_GUEST_DISCRIMINATOR];
    }
}