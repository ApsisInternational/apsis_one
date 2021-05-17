<?php

namespace Apsis\One\Model\Profile;

use Apsis\One\Model\AbstractSchema;

class Schema extends AbstractSchema
{
    /**
     * Schema constructor.
     */
    public function __construct()
    {
        $this->definition = [
            self::PROFILE_SCHEMA_TYPE_ENTRY => self::SCHEMA_PROFILE_ENTRY,
            self::PROFILE_SCHEMA_TYPE_CONSENT => self::SCHEMA_PROFILE_CONSENT,
            self::PROFILE_SCHEMA_TYPE_FIELD => array_values(array_merge(
                self::SCHEMA_FIELD_GROUP_PROFILE,
                self::SCHEMA_FIELD_GROUP_CONTEXT,
                self::SCHEMA_FIELD_CUSTOMER_ID,
                self::SCHEMA_FIELD_SUBSCRIBER_ID,
                self::SCHEMA_FIELD_IS_GUEST
            ))
        ];
        $this->definitionTypes = [
            self::PROFILE_SCHEMA_TYPE_ENTRY,
            self::PROFILE_SCHEMA_TYPE_CONSENT,
            self::PROFILE_SCHEMA_TYPE_FIELD
        ];
    }
}