<?php

namespace Apsis\One\Model\Event\Schema\Customer;

use Apsis\One\Model\AbstractSchema;

class CustomerLogin extends AbstractSchema
{
    /**
     * CustomerLogin constructor.
     *
     * @param string $discriminator
     */
    public function __construct(string $discriminator = self::EVENT_SUBSCRIBER_IS_GUEST_DISCRIMINATOR)
    {
        $this->definition = [
            $discriminator => array_merge(
                self::SCHEMA_FIELD_CUSTOMER_ID,
                self::SCHEMA_FIELD_GROUP_CONTEXT
            )
        ];
        $this->definitionTypes = [$discriminator];
    }
}