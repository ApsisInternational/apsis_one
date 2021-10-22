<?php

namespace Apsis\One\Model\Event\Schema;

use Apsis\One\Model\AbstractSchema;

class GuestUnsubscribesToNewsletter extends AbstractSchema
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->definition = [
            self::KEY_MAIN => array_merge(
                self::SCHEMA_FIELD_SUBSCRIBER_ID,
                self::SCHEMA_FIELD_GROUP_SHOP,
                self::SCHEMA_FIELD_IP_NEWSLETTER
            )
        ];
        $this->definitionTypes = [self::KEY_MAIN];
    }
}
