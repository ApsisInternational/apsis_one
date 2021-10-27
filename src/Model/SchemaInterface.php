<?php

namespace Apsis\One\Model;

use Apsis\One\Helper\HelperInterface as HI;
use Apsis\One\Model\EntityInterface as EI;

interface SchemaInterface
{
    /**
     * Array Keys
     */
    const KEY_MAIN = 'main';
    const KEY_ITEMS = 'items';
    const KEY_SCHEMA = 'schema';

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
    const VALIDATE_FORMAT_GENERIC_STRING = 'isNullOrGenericString';
    const VALIDATE_FORMAT_GENERIC_STRING_NOT_NULL = 'isGenericString';
    const VALIDATE_FORMAT_JSON_NOT_NULL = 'isJson';
    const VALIDATE_FORMAT_ADDRESS = 'isNullOrAddress';
    const VALIDATE_FORMAT_POSTCODE = 'isNullOrPostCode';
    const VALIDATE_FORMAT_CITY = 'isNullOrCityName';
    const VALIDATE_FORMAT_PHONE = 'isNullOrPhoneNumber';
    const VALIDATE_FORMAT_EMAIL_NOT_NULL = 'isEmail';
    const VALIDATE_FORMAT_DATE_TIMESTAMP = 'isNullOrDateFormatTimestamp';
    const VALIDATE_FORMAT_DATE_TIMESTAMP_NOT_NULL = 'dateFormatTimestamp';
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
        self::VALIDATE_FORMAT_GENERIC_STRING_NOT_NULL,
        self::VALIDATE_FORMAT_EMAIL_NOT_NULL,
        self::VALIDATE_FORMAT_PROFILE_UUID_NOT_NULL,
        self::VALIDATE_FORMAT_URL_NOT_NULL,
        self::VALIDATE_FORMAT_ISO_4217_CODE_NOT_NULL,
        self::VALIDATE_FORMAT_JSON_NOT_NULL,
        self::VALIDATE_FORMAT_DATE_TIMESTAMP_NOT_NULL
    ];

    /**
     * Profile schema types
     */
    const PROFILE_SCHEMA_TYPE_ENTRY = 'entry_id';
    const PROFILE_SCHEMA_TYPE_FIELDS = 'fields';
    const PROFILE_SCHEMA_TYPE_CONSENTS = 'consents';
    const PROFILE_SCHEMA_TYPE_EVENTS = 'events';

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
    const EVENT_CUSTOMER_SUB_NEWS_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.customer-sub-newsletter';
    const EVENT_CUSTOMER_UNSUB_NEWSLETTER_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.customer-unsub-newsletter';
    const EVENT_CUSTOMER_SUB_OFFERS_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.customer-sub-offers';
    const EVENT_CUSTOMER_UNSUB_OFFERS_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.customer-unsub-offers';
    const EVENT_NEWS_GUEST_OPTIN_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.non-customer-sub-newsletter';
    const EVENT_NEWS_GUEST_OPTOUT_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.non-customer-unsub-newsletter';
    const EVENT_NEWS_SUB_IS_CUSTOMER_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.subscriber-is-customer';
    const EVENT_CUSTOMER_LOGIN_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.customer-login';
    const EVENT_PRODUCT_WISHED_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.product-wished';
    const EVENT_PRODUCT_CARTED_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.product-carted';
    const EVENT_PRODUCT_REVIEWED_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.product-reviewed';
    const EVENT_ORDER_PLACED_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.order-placed';
    const EVENT_ORDER_PLACED_PRODUCT_DISCRIMINATOR = 'com.apsis1.integrations.prestashop.events.order-placed-product';

    /**
     * Schema Fields
     */
    const SCHEMA_FIELD_IP_NEWSLETTER = [
        'ipRegistrationNewsletter' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'ipRegistrationNewsletter',
            self::SCHEMA_KEY_DISPLAY_NAME => 'IP Registration Newsletter',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_IP_ADDRESS
        ]
    ];
    const SCHEMA_FIELD_CART_ID = [
        'cartId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'cartId',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Cart Id',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID_NOT_NULL
        ]
    ];
    const SCHEMA_FIELD_GROUP_REVIEW = [
        'commentId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'commentId',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Comment Id',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID_NOT_NULL
        ],
        'reviewTitle' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'reviewTitle',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Review Title',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_STRING
        ],
        'reviewDetail' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'reviewDetail',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Review Detail',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_STRING
        ],
        'reviewRating' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'reviewRating',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Review Rating',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_UNSIGNED_INT_NOT_NULL
        ],
        'reviewAuthor' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'reviewAuthor',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Review Author',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_STRING
        ]
    ];
    const SCHEMA_FIELD_CUSTOMER_ID = [
        'customerId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'customerId',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Customer Id',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID
        ]
    ];
    const SCHEMA_FIELD_SUBSCRIBER_ID = [
        'subscriberId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'subscriberId',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Subscriber Id',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID
        ]
    ];
    const SCHEMA_FIELD_GUEST_ID = [
        'guestId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'guestId',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Guest Id',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID
        ]
    ];
    const SCHEMA_FIELD_ORDER_ID = [
        'orderId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'orderId',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Order Id',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID_NOT_NULL
        ]
    ];
    const SCHEMA_FIELD_CURRENCY_CODE = [
        'currencyCode' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'currencyCode',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Currency Code',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ISO_4217_CODE_NOT_NULL
        ]
    ];
    const SCHEMA_FIELD_PRODUCT_QTY = [
        'productQty' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'productQty',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Product Qty',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_UNSIGNED_INT_NOT_NULL
        ]
    ];
    const SCHEMA_FIELD_IS_GUEST = [
        'isGuest' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'isGuest',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Is Guest',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_BOOLEAN,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_BOOLEAN
        ]
    ];
    const SCHEMA_PROFILE_CONSENT = [
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'emailNewsletter',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Email Newsletter Subscription',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_BOOLEAN,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_BOOLEAN
        ],
        [
            self::SCHEMA_KEY_LOGICAL_NAME => 'partnerOffers',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Partner Offers Subscription',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_BOOLEAN,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_BOOLEAN
        ]
    ];
    const SCHEMA_PROFILE_EVENT_ITEM_TIME = 'eventTime';
    const SCHEMA_PROFILE_EVENT_ITEM_DISCRIMINATOR = 'eventDiscriminator';
    const SCHEMA_PROFILE_EVENT_ITEM_DATA = 'eventData';
    const SCHEMA_PROFILE_EVENT = [
        'eventTime' => [
            self::SCHEMA_KEY_LOGICAL_NAME => self::SCHEMA_PROFILE_EVENT_ITEM_TIME,
            self::SCHEMA_KEY_DISPLAY_NAME => 'Event Time',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_DATE_TIMESTAMP_NOT_NULL
        ],
        'eventDiscriminator' => [
            self::SCHEMA_KEY_LOGICAL_NAME => self::SCHEMA_PROFILE_EVENT_ITEM_DISCRIMINATOR,
            self::SCHEMA_KEY_DISPLAY_NAME => 'Event Discriminator',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_STRING_NOT_NULL
        ],
        'eventData' => [
            self::SCHEMA_KEY_LOGICAL_NAME => self::SCHEMA_PROFILE_EVENT_ITEM_DATA,
            self::SCHEMA_KEY_DISPLAY_NAME => 'Event Data',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_JSON_NOT_NULL
        ]
    ];
    const SCHEMA_PROFILE_ENTRY = [
        self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
        self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_PROFILE_UUID_NOT_NULL
    ];

    /**
     * Schema Fields Group
     */
    const SCHEMA_FIELD_GROUP_SHOP = [
        'shopId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shopId',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shop Id',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID_NOT_NULL
        ],
        'shopGroupId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shopGroupId',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shop Group Id',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID_NOT_NULL
        ],
        'shopName' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shopName',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shop Name',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_STRING_NOT_NULL
        ],
        'shopGroupName' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shopGroupName',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shop Group Name',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_STRING_NOT_NULL
        ]
    ];
    const SCHEMA_FIELD_GROUP_PRODUCT_PRICE = [
        'productPriceAmountInclTax' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'productPriceAmountInclTax',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Product Price Amount Incl Tax',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'productPriceAmountExclTax' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'productPriceAmountExclTax',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Product Price Amount Excl Tax',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ]
    ];
    const SCHEMA_FIELD_GROUP_PRODUCT = [
        'productId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'productId',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Product Id',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID_NOT_NULL
        ],
        'productName' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'productName',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Product Name',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_STRING_NOT_NULL
        ],
        'productReference' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'productReference',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Product Reference',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_STRING_NOT_NULL
        ],
        'productImageUrl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'productImageUrl',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Product Image Url',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_URL
        ],
        'productUrl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'productUrl',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Product Url',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_URL
        ]
    ];
    const SCHEMA_FIELD_GROUP_SALES = [
        'isRecyclable' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'isRecyclable',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Is Recyclable',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_BOOLEAN,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_BOOLEAN
        ],
        'isGift' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'isGift',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Is Gift',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_BOOLEAN,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_BOOLEAN
        ]
    ];
    const SCHEMA_FIELD_GROUP_ORDER = [
        'orderReference' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'orderReference',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Order Reference',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_STRING_NOT_NULL
        ],
        'paymentMethod' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'paymentMethod',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Payment Method',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_STRING
        ],
        'totalDiscountsTaxIncl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalDiscountsTaxIncl',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Total Discounts Tax Incl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'totalDiscountsTaxExcl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalDiscountsTaxExcl',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Total Discounts Tax Excl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'totalPaidTaxIncl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalPaidTaxIncl',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Total Paid Tax Incl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'totalPaidTaxExcl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalPaidTaxExcl',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Total Paid Tax Excl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'totalProductsTaxIncl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalProductsTaxIncl',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Total Products Tax Incl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'totalProductsTaxExcl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalProductsTaxExcl',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Total Products Tax Excl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'totalShippingTaxIncl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalShippingTaxIncl',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Total Shipping Tax Incl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'totalShippingTaxExcl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalShippingTaxExcl',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Total Shipping Tax Excl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'shippingTaxRate' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingTaxRate',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping Tax Rate',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'totalWrappingTaxIncl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalWrappingTaxIncl',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Total Wrapping Tax Incl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'totalWrappingTaxExcl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalWrappingTaxExcl',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Total Wrapping Tax Excl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ]
    ];
    const SCHEMA_FIELD_GROUP_ORDER_PRODUCT = [
        'unitPriceTaxIncl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'unitPriceTaxIncl',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Unit Price Tax Incl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'unitPriceTaxExcl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'unitPriceTaxExcl',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Unit Price Tax Excl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'totalPriceTaxIncl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalPriceTaxIncl',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Total Price Tax Incl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'totalPriceTaxExcl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalPriceTaxExcl',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Total Price Tax Excl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'totalShippingPriceTaxIncl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalShippingPriceTaxIncl',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Total Shipping Price Tax Incl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'totalShippingPriceTaxExcl' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'totalShippingPriceTaxExcl',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Total Shipping Price Tax Excl',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ]
    ];
    const SCHEMA_FIELD_GROUP_PRODUCT_WISHED = [
        'wishlistId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'wishlistId',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Wishlist Id',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_ID_NOT_NULL
        ],
        'wishlistName' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'wishlistName',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Wishlist Name',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_STRING
        ]
    ];
    const SCHEMA_FIELD_GROUP_PROFILE = [
        'profileId' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'profileId',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Profile Id',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_PROFILE_UUID_NOT_NULL
        ],
        'defaultGroupName' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'customerGroupName',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Default Group Name',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_STRING
        ],
        'isSubscribedToNewsletter' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'emailNewsletter',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Email Newsletter?',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_BOOLEAN,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_BOOLEAN
        ],
        'newsletterDateAdded' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'newsletterDateAdded',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Newsletter Subscribe Date',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_DATE_TIMESTAMP
        ],
        'partnerOffers' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'partnerOffers',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Partner Offers?',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_BOOLEAN,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_BOOLEAN
        ],
        'languageName' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'languageName',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Language Name',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_STRING
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
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_STRING
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
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_STRING
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
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_PHONE
        ],
        'billingPhoneMobile' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'billingPhoneMobile',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Billing Mobile Phone',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_PHONE
        ],
        'billingState' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'billingState',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Billing State',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_STRING
        ],
        'billingCountry' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'billingCountry',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Billing Country',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_STRING
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
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_PHONE
        ],
        'shippingPhoneMobile' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingPhoneMobile',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping Mobile Phone',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_PHONE
        ],
        'shippingState' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingState',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping State',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_STRING
        ],
        'shippingCountry' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'shippingCountry',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Shipping Country',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_STRING,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_GENERIC_STRING
        ],
        'lifetimeTotalSpend' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'lifetimeTotalSpend',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Lifetime Total Spend',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ],
        'lifetimeTotalOrders' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'lifetimeTotalOrders',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Lifetime Total Orders',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_INT,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_UNSIGNED_INT
        ],
        'averageOrderValue' => [
            self::SCHEMA_KEY_LOGICAL_NAME => 'averageOrderValue',
            self::SCHEMA_KEY_DISPLAY_NAME => 'Average Order Value',
            self::SCHEMA_KEY_TYPE => self::DATA_TYPE_DOUBLE,
            self::SCHEMA_KEY_VALIDATE => self::VALIDATE_FORMAT_SALES_VALUE
        ]
    ];

    /** EVENT TYPE MAPPINGS  */
    const EVENT_TYPE_TO_DISCRIMINATOR_MAP = [
        EI::ET_CUST_LOGIN => self::EVENT_CUSTOMER_LOGIN_DISCRIMINATOR,
        EI::ET_NEWS_GUEST_OPTIN => self::EVENT_NEWS_GUEST_OPTIN_DISCRIMINATOR,
        EI::ET_NEWS_GUEST_OPTOUT => self::EVENT_NEWS_GUEST_OPTOUT_DISCRIMINATOR,
        EI::ET_NEWS_SUB_2_CUST => self::EVENT_NEWS_SUB_IS_CUSTOMER_DISCRIMINATOR,
        EI::ET_CUST_SUB_OFFERS => self::EVENT_CUSTOMER_SUB_OFFERS_DISCRIMINATOR,
        EI::ET_CUST_UNSUB_OFFERS => self::EVENT_CUSTOMER_UNSUB_OFFERS_DISCRIMINATOR,
        EI::ET_CUST_SUB_NEWS => self::EVENT_CUSTOMER_SUB_NEWS_DISCRIMINATOR,
        EI::ET_CUST_UNSUB_NEWS => self::EVENT_CUSTOMER_UNSUB_NEWSLETTER_DISCRIMINATOR,
        EI::ET_PRODUCT_WISHED => self::EVENT_PRODUCT_WISHED_DISCRIMINATOR,
        EI::ET_PRODUCT_CARTED => self::EVENT_PRODUCT_CARTED_DISCRIMINATOR,
        EI::ET_PRODUCT_REVIEWED => self::EVENT_PRODUCT_REVIEWED_DISCRIMINATOR,
        EI::ET_ORDER_PLACED => [
            self::KEY_MAIN => self::EVENT_ORDER_PLACED_DISCRIMINATOR,
            self::KEY_ITEMS => self::EVENT_ORDER_PLACED_PRODUCT_DISCRIMINATOR
        ]
    ];
    const EVENT_TYPE_TO_SCHEMA_MAP = [
        EI::ET_CUST_LOGIN => HI::SERVICE_EVENT_CUSTOMER_LOGIN_SCHEMA,
        EI::ET_NEWS_SUB_2_CUST => HI::SERVICE_EVENT_NEWSLETTER_SUB_IS_CUSTOMER_SCHEMA,
        EI::ET_NEWS_GUEST_OPTIN => HI::SERVICE_EVENT_NEWSLETTER_GUEST_OPTIN_SCHEMA,
        EI::ET_NEWS_GUEST_OPTOUT => HI::SERVICE_EVENT_NEWSLETTER_GUEST_OPTOUT_SCHEMA,
        EI::ET_CUST_SUB_OFFERS => HI::SERVICE_EVENT_CUSTOMER_OPTIN_OFFERS_SCHEMA,
        EI::ET_CUST_UNSUB_OFFERS => HI::SERVICE_EVENT_CUSTOMER_OPTOUT_OFFERS_SCHEMA,
        EI::ET_CUST_SUB_NEWS => HI::SERVICE_EVENT_CUSTOMER_OPTIN_NEWSLETTER_SCHEMA,
        EI::ET_CUST_UNSUB_NEWS => HI::SERVICE_EVENT_CUSTOMER_OPTOUT_NEWSLETTER_SCHEMA,
        EI::ET_PRODUCT_WISHED => HI::SERVICE_EVENT_CUSTOMER_PRODUCT_WISHED_SCHEMA,
        EI::ET_PRODUCT_CARTED => HI::SERVICE_EVENT_COMMON_PRODUCT_CARTED_SCHEMA,
        EI::ET_PRODUCT_REVIEWED => HI::SERVICE_EVENT_COMMON_PRODUCT_REVIEWED_SCHEMA,
        EI::ET_ORDER_PLACED => HI::SERVICE_EVENT_COMMON_ORDER_PLACED_SCHEMA
    ];
    const EVENTS_CONTAINING_PRODUCT = [
        EI::ET_PRODUCT_REVIEWED,
        EI::ET_PRODUCT_WISHED,
        EI::ET_PRODUCT_CARTED,
        EI::ET_ORDER_PLACED
    ];

    /**
     * Class constructor.
     */
    public function __construct();

    /**
     * @return array
     */
    public function getDefinition(): array;

    /**
     * @return array
     */
    public function getDefinitionTypes(): array;
}
