<?php

namespace Apsis\One\Model;

interface SchemaInterface
{
    /**
     * Array Keys
     */
    const KEY_ITEMS = 'items';
    const KEY_SCHEMA = 'schema';
    const KEY_PROVIDER = 'container';

    /**
     * Data types
     */
    const DATA_TYPE_INT = 'integer';
    const DATA_TYPE_STRING = 'string';
    const DATA_TYPE_DOUBLE = 'double';
    const DATA_TYPE_BOOLEAN = 'boolean';

    /**
     * Validation types
     */
    const VALIDATE_FORMAT_UNSIGNED_INT = 'isNullOrUnsignedInt';
    const VALIDATE_FORMAT_UNSIGNED_INT_NOT_NULL = 'isUnsignedInt';
    const VALIDATE_FORMAT_ID = 'isNullOrUnsignedId';
    const VALIDATE_FORMAT_ID_NOT_NULL = 'isUnsignedId';
    const VALIDATE_FORMAT_NAME = 'isNullOrCustomerName';
    const VALIDATE_FORMAT_GENERIC_NAME = 'isNullOrGenericName';
    const VALIDATE_FORMAT_GENERIC_NAME_NOT_NULL = 'isGenericName';
    const VALIDATE_FORMAT_ADDRESS = 'isNullOrAddress';
    const VALIDATE_FORMAT_POSTCODE = 'isNullOrPostCode';
    const VALIDATE_FORMAT_CITY = 'isNullOrCityName';
    const VALIDATE_FORMAT_PHONE = 'isNullOrPhoneNumber';
    const VALIDATE_FORMAT_EMAIL_NOT_NULL = 'isEmail';
    const VALIDATE_FORMAT_DATE_TIMESTAMP = 'isNullOrDateFormatTimestamp';
    const VALIDATE_FORMAT_PROFILE_UUID_NOT_NULL = 'isIntegrationProfileId';
    const VALIDATE_FORMAT_SALES_VALUE = 'isNullOrSalesValue';
    const VALIDATE_FORMAT_BOOLEAN = 'isNullOrBoolean';
    const VALIDATE_FORMAT_URL = 'isNullOrUrl';
    const VALIDATE_FORMAT_URL_NOT_NULL = 'isUrl';
    const VALIDATE_FORMAT_IP_ADDRESS = 'isNullOrIpAddress';
    const VALIDATE_FORMAT_ISO_4217_CODE_NOT_NULL = 'IsCurrencyCode';

    const VALID_GENERIC_NAME_PATTERN = '/^[a-zA-Z0-9.,_-]+$/';

    /**
     * Array containing validations which should always have a value
     */
    const NOT_NULL_VALIDATIONS = [
        self::VALIDATE_FORMAT_UNSIGNED_INT_NOT_NULL,
        self::VALIDATE_FORMAT_ID_NOT_NULL,
        self::VALIDATE_FORMAT_GENERIC_NAME_NOT_NULL,
        self::VALIDATE_FORMAT_EMAIL_NOT_NULL,
        self::VALIDATE_FORMAT_PROFILE_UUID_NOT_NULL,
        self::VALIDATE_FORMAT_URL_NOT_NULL,
        self::VALIDATE_FORMAT_ISO_4217_CODE_NOT_NULL
    ];

    /**
     * Profile schema types
     */
    const PROFILE_SCHEMA_TYPE_ENTRY = 'entry_id';
    const PROFILE_SCHEMA_TYPE_FIELD = 'fields';
    const PROFILE_SCHEMA_TYPE_CONSENT = 'consents';

    /**
     * Profile schema field keys
     */
    const SCHEMA_KEY_LOGICAL_NAME = 'logical_name';
    const SCHEMA_KEY_DISPLAY_NAME = 'display_name';
    const SCHEMA_KEY_TYPE = 'type';
    const SCHEMA_KEY_VALIDATE = 'validate';
    const SCHEMA_ENTRY_ID_FIELD_NAME = 'entryId';

    /**
     * Events discriminator
     */
    const EVENT_CUSTOMER_IS_SUBSCRIBER_DISCRIMINATOR =
        'com.apsis1.integrations.prestashop.events.customer-is-subscriber';
    const EVENT_CUSTOMER_LOGIN_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.customer-login';
    const EVENT_CUSTOMER_PRODUCT_WISHED_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.product-wished';
    const EVENT_GUEST_IS_CUSTOMER_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.guest-is-customer';
    const EVENT_GUEST_IS_SUBSCRIBER_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.guest-is-subscriber';
    const EVENT_SUBSCRIBER_IS_GUEST_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.subscriber-is-guest';
    const EVENT_SUBSCRIBER_IS_CUSTOMER_DISCRIMINATOR =
        'com.apsis1.integrations.prestashop.events.subscriber-is-customer';
    const EVENT_SUBSCRIBER_UNSUBSCRIBE_DISCRIMINATOR =
        'com.apsis1.integrations.prestashop.events.subscriber-unsubscribes';
    const EVENT_COMMON_PRODUCT_CARTED_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.product-carted';
    const EVENT_COMMON_PRODUCT_REVIEWED_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.product-reviewed';
    const EVENT_COMMON_ORDER_PLACED_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.order-placed';
    const EVENT_COMMON_ORDER_PLACED_PRODUCT_DISCRIMINATOR =
        'com.apsis1.integrations.prestashop.events.order-placed-product';
    const EVENT_COMMON_CART_ABANDONED_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.cart-abandoned';
    const EVENT_COMMON_CART_ABANDONED_PRODUCT_DISCRIMINATOR =
        'com.apsis1.integrations.prestashop.events.cart-abandoned-product';

    /**
     * Schema Fields
     */
    const SCHEMA_FIELD_IP_NEWSLETTER = [
        'ipRegistrationNewsletter' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'ipRegistrationNewsletter',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_IP_ADDRESS
        ]
    ];
    const SCHEMA_FIELD_CART_ID = [
        'cartId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'cartId',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID_NOT_NULL
        ]
    ];
    const SCHEMA_FIELD_COMMENT_ID = [
        'commentId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'commentId',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID_NOT_NULL
        ]
    ];
    const SCHEMA_FIELD_CUSTOMER_ID = [
        'customerId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'customerId',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID
        ]
    ];
    const SCHEMA_FIELD_SUBSCRIBER_ID = [
        'subscriberId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'subscriberId',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID
        ]
    ];
    const SCHEMA_FIELD_GUEST_ID = [
        'guestId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'guestId',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID
        ]
    ];
    const SCHEMA_FIELD_ORDER_ID = [
        'orderId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'orderId',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID_NOT_NULL
        ]
    ];
    const SCHEMA_FIELD_CURRENCY_CODE = [
        'currencyCode' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'currencyCode',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ISO_4217_CODE_NOT_NULL
        ]
    ];
    const SCHEMA_FIELD_PRODUCT_QTY = [
        'productQty' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'productQty',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_UNSIGNED_INT_NOT_NULL
        ]
    ];
    const SCHEMA_FIELD_IS_GUEST = [
        'isGuest' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'isGuest',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_BOOLEAN,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_BOOLEAN
        ]
    ];
    const SCHEMA_PROFILE_CONSENT = [
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'newsletterSubscription',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Newsletter Subscription',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_BOOLEAN,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_BOOLEAN
        ]
    ];
    const SCHEMA_PROFILE_ENTRY = [
        self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
        self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_PROFILE_UUID_NOT_NULL
    ];

    /**
     * Schema Fields Group
     */
    const SCHEMA_FIELD_GROUP_CONTEXT = [
        'shopId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shopId',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID_NOT_NULL
        ],
        'shopGroupId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shopGroupId',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID_NOT_NULL
        ],
        'shopName' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shopName',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME_NOT_NULL
        ],
        'shopGroupName' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shopGroupName',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME_NOT_NULL
        ]
    ];
    const SCHEMA_FIELD_GROUP_PRODUCT_PRICE = [
        'productPriceAmountInclTax' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'productPriceAmountInclTax',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'productPriceAmountExclTax' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'productPriceAmountExclTax',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ]
    ];
    const SCHEMA_FIELD_GROUP_PRODUCT = [
        'productId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'productId',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID_NOT_NULL
        ],
        'productName' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'productName',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME_NOT_NULL
        ],
        'productReference' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'productReference',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME_NOT_NULL
        ],
        'productImageUrl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'productImageUrl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_URL
        ],
        'productUrl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'productUrl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_URL
        ]
    ];
    const SCHEMA_FIELD_GROUP_SALES = [
        'isRecyclable' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'isRecyclable',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_BOOLEAN,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_BOOLEAN
        ],
        'isGift' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'isGift',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_BOOLEAN,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_BOOLEAN
        ]
    ];
    const SCHEMA_FIELD_GROUP_ORDER = [
        'orderReference' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'orderReference',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME_NOT_NULL
        ],
        'paymentMethod' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'paymentMethod',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ],
        'totalDiscounts' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalDiscounts',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'totalPaid' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalPaid',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'totalPaidReal' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalPaidReal',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'totalProducts' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalProducts',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'totalShipping' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalShipping',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'totalWrapping' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalWrapping',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ]
    ];
    const SCHEMA_FIELD_GROUP_ORDER_PRODUCT = [
        'orderRowId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'orderRowId',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID_NOT_NULL
        ],
        'productPrice' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'productPrice',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'unitPriceTaxIncl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'unitPriceTaxIncl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'unitPriceTaxExcl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'unitPriceTaxExcl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ]
    ];
    const SCHEMA_FIELD_GROUP_PRODUCT_WISHED = [
        'wishlistId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'wishlistId',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID_NOT_NULL
        ],
        'wishlistName' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'wishlistName',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ]
    ];
    const SCHEMA_FIELD_GROUP_PROFILE = [
        'profileId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'profileId',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Profile Id',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_PROFILE_UUID_NOT_NULL
        ],
        'customerGroup' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'customerGroup',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Customer Group',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ],
        'isSubscribedToNewsletter' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'isSubscribedToNewsletter',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Is Subscribed To Newsletter',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_BOOLEAN,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_BOOLEAN
        ],
        'newsletterDateAdded' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'newsletterDateAdded',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Newsletter Subscribe Date',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_DATE_TIMESTAMP
        ],
        'isActive' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'isActive',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Is Active',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_BOOLEAN,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_BOOLEAN
        ],
        'languageName' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'languageName',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Language Name',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ],
        'dateAdded' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'dateAdded',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Customer Creation Date',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_DATE_TIMESTAMP
        ],
        'firstName' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'firstName',
            self::SCHEMA_KEY_DISPLAY_NAME => 'First Name',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_NAME
        ],
        'lastName' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'lastName',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Last Name',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_NAME
        ],
        'alias' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'alias',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Alias',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ],
        'email' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'email',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Email',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_EMAIL_NOT_NULL
        ],
        'gender' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'gender',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Gender',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ],
        'birthday' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'birthday',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Birthday',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_DATE_TIMESTAMP
        ],
        'company' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'company',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Company',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ],
        'billingAddress1' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'billingAddress1',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Billing Address Line 1',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ADDRESS
        ],
        'billingAddress2' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'billingAddress2',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Billing Address Line 2',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ADDRESS
        ],
        'billingPostcode' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'billingPostcode',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Billing Postcode',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_POSTCODE
        ],
        'billingCity' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'billingCity',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Billing City',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_CITY
        ],
        'billingPhone' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'billingPhone',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Billing Phone',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_PHONE
        ],
        'billingPhoneMobile' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'billingPhoneMobile',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Billing Mobile Phone',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_PHONE
        ],
        'billingState' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'billingState',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Billing State',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ],
        'billingCountry' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'billingCountry',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Billing Country',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ],
        'shippingAddress1' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingAddress1',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping Address Line 1',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ADDRESS
        ],
        'shippingAddress2' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingAddress2',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping Address Line 2',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ADDRESS
        ],
        'shippingPostcode' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingPostcode',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping Postcode',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_POSTCODE
        ],
        'shippingCity' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingCity',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping City',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_CITY
        ],
        'shippingPhone' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingPhone',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping Phone',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_PHONE
        ],
        'shippingPhoneMobile' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingPhoneMobile',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping Mobile Phone',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_PHONE
        ],
        'shippingState' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingState',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping State',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ],
        'shippingCountry' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingCountry',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping Country',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_NAME
        ],
        'lifetimeTotalSpend' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'lifetimeTotalSpend',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Lifetime Total Spend',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'averageOrderValue' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'averageOrderValue',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Average Order Value',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ]
    ];

    /**
     * @return array
     */
    public function getDefinition(): array;

    /**
     * @return array
     */
    public function getDefinitionTypes(): array;
}