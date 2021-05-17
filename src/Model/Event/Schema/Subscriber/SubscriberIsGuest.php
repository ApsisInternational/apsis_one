<?php

namespace Apsis\One\Model\Event\Schema\Subscriber;

use Apsis\One\Model\AbstractSchema;

class SubscriberIsGuest extends AbstractSchema
{
    /**
     * SubscriberIsGuest constructor.
     *
     * @param string $discriminator
     */
    public function __construct(string $discriminator = self::EVENT_SUBSCRIBER_IS_GUEST_DISCRIMINATOR)
    {
        $this->definition = [
            $discriminator => array_merge(
                self::SCHEMA_FIELD_GUEST_ID,
                self::SCHEMA_FIELD_CUSTOMER_ID,
                self::SCHEMA_FIELD_GROUP_CONTEXT,
                self::SCHEMA_FIELD_IP_NEWSLETTER
            )
        ];
        $this->definitionTypes = [$discriminator];
    }
}