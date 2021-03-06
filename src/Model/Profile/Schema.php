<?php

namespace Apsis\One\Model\Profile;

use Apsis\One\Model\AbstractSchema;

class Schema extends AbstractSchema
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->definition = [
            self::PROFILE_SCHEMA_TYPE_ENTRY => self::SCHEMA_PROFILE_ENTRY,
            self::PROFILE_SCHEMA_TYPE_CONSENTS => self::SCHEMA_PROFILE_CONSENT,
            self::PROFILE_SCHEMA_TYPE_FIELDS => array_values(array_merge(
                self::SCHEMA_FIELD_GROUP_PROFILE,
                self::SCHEMA_FIELD_GROUP_SHOP,
                self::SCHEMA_FIELD_CUSTOMER_ID,
                self::SCHEMA_FIELD_SUBSCRIBER_ID,
                self::SCHEMA_FIELD_IS_GUEST
            )),
            self::PROFILE_SCHEMA_TYPE_EVENTS => array_values(self::SCHEMA_PROFILE_EVENT)
        ];
        $this->definitionTypes = [
            self::PROFILE_SCHEMA_TYPE_ENTRY,
            self::PROFILE_SCHEMA_TYPE_CONSENTS,
            self::PROFILE_SCHEMA_TYPE_FIELDS,
            self::PROFILE_SCHEMA_TYPE_EVENTS
        ];
    }
}
