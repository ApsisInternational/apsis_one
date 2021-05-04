<?php

namespace Apsis\One\Model\Profile;

class Schema
{
    /**
     * Data types
     */
    const TYPE_INT = 'integer';
    const TYPE_STRING = 'string';
    const TYPE_DOUBLE = 'double';
    const TYPE_BOOLEAN = 'boolean';

    /**
     * Validation types
     */
    const VALIDATE_FORMAT_ID = 'isNullOrUnsignedId';
    const VALIDATE_FORMAT_ID_NOT_NULL = 'isUnsignedId';
    const VALIDATE_FORMAT_NAME = 'isNullOrCustomerName';
    const VALIDATE_FORMAT_GENERIC_NAME = 'isNullOrGenericName';
    const VALIDATE_FORMAT_GENERIC_NAME_NOT_NULL = 'isGenericName';
    const VALIDATE_FORMAT_ADDRESS = 'isNullOrAddress';
    const VALIDATE_FORMAT_POSTCODE = 'isNullOrPostCode';
    const VALIDATE_FORMAT_CITY = 'isNullOrCityName';
    const VALIDATE_FORMAT_PHONE = 'isNullOrPhoneNumber';
    const VALIDATE_FORMAT_EMAIL = 'isEmail';
    const VALIDATE_FORMAT_DATE_TIMESTAMP = 'isNullOrDateFormatTimestamp';
    const VALIDATE_FORMAT_PROFILE_UUID = 'isIntegrationProfileId';
    const VALIDATE_FORMAT_SALES_VALUE = 'isNullOrSalesValue';
    const VALIDATE_FORMAT_BOOLEAN = 'isNullOrBoolean';

    /**
     * Schema types
     */
    const SCHEMA_TYPE_ENTRY = 'entry_id';
    const SCHEMA_TYPE_FIELD = 'fields';
    const SCHEMA_TYPE_CONSENT = 'consents';

    /**
     * Schema field keys
     */
    const SCHEMA_KEY_LOGICAL_NAME = 'logical_name';
    const SCHEMA_KEY_DISPLAY_NAME = 'display_name';
    const SCHEMA_KEY_TYPE = 'type';
    const SCHEMA_KEY_VALIDATE = 'validate';

    /**
     * Array containing all schema types
     */
    const SCHEMA_TYPES = [
        self::SCHEMA_TYPE_ENTRY,
        self::SCHEMA_TYPE_CONSENT,
        self::SCHEMA_TYPE_FIELD
    ];

    /**
     * Array containing validations which should always have a value
     */
    const NOT_NULL_VALIDATIONS = [
        self::VALIDATE_FORMAT_ID_NOT_NULL,
        self::VALIDATE_FORMAT_EMAIL,
        self::VALIDATE_FORMAT_PROFILE_UUID
    ];

    /**
     * @var array
     */
    private $ksId = [
        self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
        self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_PROFILE_UUID
    ];

    /**
     * @var array
     */
    private $fields = [
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'profileId',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Profile Id',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_PROFILE_UUID
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'customerId',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Customer Id',
            self::SCHEMA_KEY_TYPE => self::TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'subscriptionId',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Subscription Id',
            self::SCHEMA_KEY_TYPE => self::TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shopId',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shop Id',
            self::SCHEMA_KEY_TYPE => self::TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID_NOT_NULL
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shopGroupId',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shop Group Id',
            self::SCHEMA_KEY_TYPE => self::TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID_NOT_NULL
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shopName',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shop Name',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME_NOT_NULL
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shopGroupName',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shop Group Name',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME_NOT_NULL
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'customerGroup',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Customer Group',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'isSubscribedToNewsletter',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Is Subscribed To Newsletter',
            self::SCHEMA_KEY_TYPE => self::TYPE_BOOLEAN,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_BOOLEAN
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'newsletterDateAdded',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Newsletter Subscribe Date',
            self::SCHEMA_KEY_TYPE => self::TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_DATE_TIMESTAMP
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'isGuest',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Is Guest',
            self::SCHEMA_KEY_TYPE => self::TYPE_BOOLEAN,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_BOOLEAN
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'isActive',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Is Active',
            self::SCHEMA_KEY_TYPE => self::TYPE_BOOLEAN,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_BOOLEAN
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'languageName',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Language Name',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'dateAdded',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Customer Creation Date',
            self::SCHEMA_KEY_TYPE => self::TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_DATE_TIMESTAMP
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'firstName',
            self::SCHEMA_KEY_DISPLAY_NAME => 'First Name',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_NAME
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'lastName',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Last Name',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_NAME
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'alias',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Alias',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'email',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Email',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_EMAIL
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'gender',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Gender',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'birthday',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Birthday',
            self::SCHEMA_KEY_TYPE => self::TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_DATE_TIMESTAMP
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'company',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Company',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'billingAddress1',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Billing Address Line 1',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ADDRESS
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'billingAddress2',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Billing Address Line 2',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ADDRESS
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'billingPostcode',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Billing Postcode',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_POSTCODE
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'billingCity',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Billing City',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_CITY
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'billingPhone',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Billing Phone',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_PHONE
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'billingPhoneMobile',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Billing Mobile Phone',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_PHONE
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'billingState',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Billing State',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'billingCountry',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Billing Country',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingAddress1',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping Address Line 1',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ADDRESS
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingAddress2',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping Address Line 2',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ADDRESS
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingPostcode',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping Postcode',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_POSTCODE
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingCity',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping City',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_CITY
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingPhone',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping Phone',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_PHONE
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingPhoneMobile',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping Mobile Phone',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_PHONE
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingState',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping State',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingCountry',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping Country',
            self::SCHEMA_KEY_TYPE => self::TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'lifetimeTotalSpend',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Lifetime Total Spend',
            self::SCHEMA_KEY_TYPE => self::TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
    ];

    /**
     * @var array
     */
    private $consents = [
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'newsletterSubscription',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Newsletter Subscription',
            self::SCHEMA_KEY_TYPE => self::TYPE_BOOLEAN,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_BOOLEAN
        ]
    ];

    /**
     * @return array
     */
    public function getProfileSchema()
    {
        return [
            self::SCHEMA_TYPE_ENTRY => $this->ksId,
            self::SCHEMA_TYPE_CONSENT => $this->consents,
            self::SCHEMA_TYPE_FIELD => $this->fields,
        ];
    }

}