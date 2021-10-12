<?php

namespace Apsis\One\Model;

use PrestaShop\PrestaShop\Core\Foundation\Database\EntityInterface as PsEntityInterface;
use ObjectModel;

interface EntityInterface extends PsEntityInterface
{
    /** TABLE PREFIX */
    const T_PREFIX = 'apsis';

    /** TABLES NAME */
    const T_PROFILE = self::T_PREFIX . '_profile';
    const T_EVENT = self::T_PREFIX . '_event';
    const T_ABANDONED_CART = self::T_PREFIX . '_abandoned_cart';
    const TABLES = [
        self::T_PROFILE,
        self::T_EVENT,
        self::T_ABANDONED_CART
    ];

    /** TABLES LABEL */
    const T_PROFILE_LABEL = 'Profiles';
    const T_EVENT_LABEL = 'Events';
    const T_ABANDONED_CART_LABEL = 'Abandoned Carts';

    /** TABLES ALIAS */
    const T_PROFILE_ALIAS = 'ap';
    const T_EVENT_ALIAS = 'ae';
    const T_ABANDONED_CART_ALIAS = 'aac';

    /** COLUMNS NAME */
    const C_ID_PROFILE = 'id_' . self::T_PROFILE;
    const C_ID_INTEGRATION = 'id_integration';
    const C_ID_SHOP = 'id_shop';
    const C_ID_ENTITY_PS = 'id_entity_ps';
    const C_ID_CUSTOMER = 'id_customer';
    const C_ID_NEWSLETTER = 'id_newsletter';
    const C_ID_AC = 'id_' . self::T_ABANDONED_CART;
    const C_ID_EVENT = 'id_' . self::T_EVENT;
    const C_ID_CART = 'id_cart';
    const C_SYNC_STATUS = 'sync_status';
    const C_EVENT_TYPE = 'event_type';
    const C_IS_CUSTOMER = 'is_customer';
    const C_IS_NEWSLETTER = 'is_newsletter';
    const C_IS_OFFERS = 'is_offers';
    const C_IS_GUEST = 'is_guest';
    const C_EMAIL = 'email';
    const C_TOKEN = 'token';
    const C_ERROR_MSG = 'error_message';
    const C_PROFILE_DATA = 'profile_data';
    const C_EVENT_DATA = 'event_data';
    const C_SUB_EVENT_DATA = 'sub_event_data';
    const C_CART_DATA = 'cart_data';
    const C_DATE_ADD = 'date_add';
    const C_DATE_UPD = 'date_upd';

    /** COLUMNS LABEL */
    const C_ID_PROFILE_LABEL = 'Profile Id';
    const C_ID_INTEGRATION_LABEL = 'Profile Key';
    const C_ID_SHOP_LABEL = 'Shop Id';
    const C_ID_ENTITY_PS_LABEL = 'PS Entity';
    const C_ID_CUSTOMER_LABEL = 'Customer Id';
    const C_ID_NEWSLETTER_LABEL = 'Newsletter Id';
    const C_ID_AC_LABEL = 'AC Id';
    const C_ID_EVENT_LABEL = 'Event Id';
    const C_ID_CART_LABEL = 'Cart Id';
    const C_SYNC_STATUS_LABEL = 'Sync';
    const C_EVENT_TYPE_LABEL = 'Type';
    const C_IS_CUSTOMER_LABEL = 'Customer';
    const C_IS_NEWSLETTER_LABEL = 'Newsletter';
    const C_IS_OFFERS_LABEL = 'Partner Offers';
    const C_IS_GUEST_LABEL = 'Guest';
    const C_EMAIL_LABEL = 'Email';
    const C_TOKEN_LABEL = 'Token';
    const C_ERROR_MSG_LABEL = 'Error';
    const C_PROFILE_DATA_LABEL = 'Profile Data';
    const C_EVENT_DATA_LABEL = 'Event Data';
    const C_SUB_EVENT_DATA_LABEL = 'Sub Event Data';
    const C_CART_DATA_LABEL = 'Cart Data';
    const C_DATE_ADD_LABEL = 'Added At';
    const C_DATE_UPD_LABEL = 'Updated At';

    /** COLUMNS DEFINITION */
    const CD_TYPE_ID = [
        'type' => ObjectModel::TYPE_INT,
        'required' => true,
        'validate' => 'isUnsignedId',
        'copy_post' => false
    ];
    const CD_TYPE_INT = [
        'type' => ObjectModel::TYPE_INT,
        'required' => true,
        'validate' => 'isInt',
        'copy_post' => false
    ];
    const CD_TYPE_BOOLEAN = [
        'type' => ObjectModel::TYPE_INT,
        'validate' => 'isBool',
        'copy_post' => false
    ];
    const CD_TYPE_STRING_UUID = [
        'type' => ObjectModel::TYPE_STRING,
        'required' => true,
        'size' => 36,
        'validate' => 'isString',
        'copy_post' => false
    ];
    const CD_TYPE_STRING_JSON = [
        'type' => ObjectModel::TYPE_STRING,
        'required' => true,
        'validate' => 'isJson',
        'copy_post' => false
    ];
    const CD_TYPE_DATE = [
        'type' => ObjectModel::TYPE_DATE,
        'required' => true,
        'validate' => 'isDate',
        'copy_post' => false
    ];
    const CD_ID_PROFILE = self::CD_TYPE_ID;
    const CD_ID_SHOP = self::CD_TYPE_ID;
    const CD_ID_ENTITY_PS = self::CD_TYPE_ID;
    const CD_ID_CUSTOMER = self::CD_TYPE_ID;
    const CD_ID_NEWSLETTER = self::CD_TYPE_ID;
    const CD_ID_AC = self::CD_TYPE_ID;
    const CD_ID_EVENT = self::CD_TYPE_ID;
    const CD_ID_CART = self::CD_TYPE_ID;
    const CD_SYNC_STATUS = self::CD_TYPE_INT;
    const CD_EVENT_TYPE = self::CD_TYPE_INT;
    const CD_IS_CUSTOMER = self::CD_TYPE_BOOLEAN;
    const CD_IS_NEWSLETTER = self::CD_TYPE_BOOLEAN;
    const CD_IS_OFFERS = self::CD_TYPE_BOOLEAN;
    const CD_IS_GUEST = self::CD_TYPE_BOOLEAN;
    const CD_ID_INTEGRATION = self::CD_TYPE_STRING_UUID;
    const CD_TOKEN = self::CD_TYPE_STRING_UUID;
    const CD_PROFILE_DATA = self::CD_TYPE_STRING_JSON;
    const CD_EVENT_DATA = self::CD_TYPE_STRING_JSON;
    const CD_SUB_EVENT_DATA = self::CD_TYPE_STRING_JSON;
    const CD_CART_DATA = self::CD_TYPE_STRING_JSON;
    const CD_DATE_ADD = self::CD_TYPE_DATE;
    const CD_DATE_UPD = self::CD_TYPE_DATE;
    const CD_EMAIL = [
        'type' => ObjectModel::TYPE_STRING,
        'required' => true,
        'size' => 255,
        'validate' => 'isEmail',
        'copy_post' => false
    ];
    const CD_ERROR_MSG = [
        'type' => ObjectModel::TYPE_STRING,
        'size' => 255,
        'validate' => 'isString',
        'copy_post' => false
    ];

    /** BOOLEAN IS */
    const YES = true;
    const NO = false;

    /** SYNC STATUS */
    const SS_PENDING = 0;
    const SS_SYNCED = 1;
    const SS_FAILED = 2;
    const SS_JUSTIN = 3;

    /** EVENT TYPES @todo change these */
    const ET_CUSTOMER = 1;
    const ET_SUBSCRIBER = 2;
    const ET_GUEST = 3;

    /** MAPPINGS */
    const T_LABEL_MAPPINGS = [
        self::T_PROFILE => self::T_PROFILE_LABEL,
        self::T_EVENT => self::T_EVENT_LABEL,
        self::T_ABANDONED_CART => self::T_ABANDONED_CART_LABEL,
    ];
    const T_ALIAS_MAPPINGS = [
        self::T_PROFILE => self::T_PROFILE_ALIAS,
        self::T_EVENT => self::T_EVENT_ALIAS,
        self::T_ABANDONED_CART => self::T_ABANDONED_CART_ALIAS,
    ];
    const C_LABEL_MAPPINGS = [
        self::C_ID_PROFILE => self::C_ID_PROFILE_LABEL,
        self::C_ID_INTEGRATION => self::C_ID_INTEGRATION_LABEL,
        self::C_ID_SHOP => self::C_ID_SHOP_LABEL,
        self::C_ID_ENTITY_PS => self::C_ID_ENTITY_PS_LABEL,
        self::C_ID_CUSTOMER => self::C_ID_CUSTOMER_LABEL,
        self::C_ID_NEWSLETTER => self::C_ID_NEWSLETTER_LABEL,
        self::C_ID_AC => self::C_ID_AC_LABEL,
        self::C_ID_EVENT => self::C_ID_EVENT_LABEL,
        self::C_ID_CART => self::C_ID_CART_LABEL,
        self::C_SYNC_STATUS => self::C_SYNC_STATUS_LABEL,
        self::C_EVENT_TYPE => self::C_EVENT_TYPE_LABEL,
        self::C_IS_CUSTOMER => self::C_IS_CUSTOMER_LABEL,
        self::C_IS_NEWSLETTER => self::C_IS_NEWSLETTER_LABEL,
        self::C_IS_OFFERS => self::C_IS_OFFERS_LABEL,
        self::C_IS_GUEST => self::C_IS_GUEST_LABEL,
        self::C_EMAIL => self::C_EMAIL_LABEL,
        self::C_TOKEN => self::C_TOKEN_LABEL,
        self::C_ERROR_MSG => self::C_ERROR_MSG_LABEL,
        self::C_PROFILE_DATA => self::C_PROFILE_DATA_LABEL,
        self::C_EVENT_DATA => self::C_EVENT_DATA_LABEL,
        self::C_SUB_EVENT_DATA => self::C_SUB_EVENT_DATA_LABEL,
        self::C_CART_DATA => self::C_CART_DATA_LABEL,
        self::C_DATE_ADD => self::C_DATE_ADD_LABEL,
        self::C_DATE_UPD => self::C_DATE_UPD_LABEL,
        self::PS_T_SHOP_C_NAME_ALIAS => self::PS_TABLE_SHOP
    ];
    const T_COLUMNS_MAPPINGS = [
        self::T_PROFILE => [
            self::C_ID_INTEGRATION => self::CD_ID_INTEGRATION,
            self::C_ID_SHOP => self::CD_ID_SHOP,
            self::C_EMAIL => self::CD_EMAIL,
            self::C_ID_CUSTOMER => self::CD_ID_CUSTOMER,
            self::C_ID_NEWSLETTER => self::CD_ID_NEWSLETTER,
            self::C_IS_CUSTOMER => self::CD_IS_CUSTOMER,
            self::C_IS_GUEST => self::CD_IS_GUEST,
            self::C_IS_NEWSLETTER => self::CD_IS_NEWSLETTER,
            self::C_IS_OFFERS => self::CD_IS_OFFERS,
            self::C_PROFILE_DATA => self::CD_PROFILE_DATA,
            self::C_SYNC_STATUS => self::CD_SYNC_STATUS,
            self::C_ERROR_MSG => self::CD_ERROR_MSG,
            self::C_DATE_UPD => self::CD_DATE_UPD,
        ],
        self::T_EVENT => [
            self::C_ID_PROFILE => self::CD_ID_PROFILE,
            self::C_ID_SHOP => self::CD_ID_SHOP,
            self::C_ID_ENTITY_PS => self::CD_ID_ENTITY_PS,
            self::C_EVENT_TYPE => self::CD_EVENT_TYPE,
            self::C_EVENT_DATA => self::CD_EVENT_DATA,
            self::C_SUB_EVENT_DATA => self::CD_SUB_EVENT_DATA,
            self::C_SYNC_STATUS => self::CD_SYNC_STATUS,
            self::C_ERROR_MSG => self::CD_ERROR_MSG,
            self::C_DATE_ADD => self::CD_DATE_ADD,
            self::C_DATE_UPD => self::CD_DATE_UPD
        ],
        self::T_ABANDONED_CART => [
            self::C_ID_PROFILE => self::CD_ID_PROFILE,
            self::C_ID_SHOP => self::CD_ID_SHOP,
            self::C_ID_CART => self::CD_ID_CART,
            self::C_CART_DATA => self::CD_CART_DATA,
            self::C_TOKEN => self::CD_TOKEN,
            self::C_DATE_UPD => self::CD_DATE_UPD,
        ]
    ];
    const T_PRIMARY_MAPPINGS = [
        self::T_PROFILE => self::C_ID_PROFILE,
        self::T_EVENT => self::C_ID_EVENT,
        self::T_ABANDONED_CART => self::C_ID_AC
    ];
    const C_PRIMARY_DEF = [
        self::C_ID_PROFILE => self::CD_ID_PROFILE,
        self::C_ID_EVENT => self::CD_ID_EVENT,
        self::C_ID_AC => self::CD_ID_AC
    ];
    const COLUMN_SS_LABEL_MAPPINGS = [
        self::SS_PENDING => 'Pending',
        self::SS_SYNCED => 'Synced',
        self::SS_FAILED => 'Failed',
        self::SS_JUSTIN => 'JUSTIN Sync'
    ];
    /** @todo change these */
    const COLUMN_ET_LABEL_MAPPINGS = [
        self::ET_CUSTOMER => 'Customer',
        self::ET_SUBSCRIBER => 'Subscriber',
        self::ET_GUEST => 'Guest'
    ];

    const PS_TABLE_SHOP = 'shop';
    const PS_TABLE_SHOP_ALIAS = 's';
    const PS_T_SHOP_C_NAME = 'name';
    const PS_T_SHOP_C_NAME_ALIAS = 'shop_name';
    const PS_SHOP_ID_PARAM = 'context_shop_id';

    //** OTHER */
    const EMPTY = '';
    const NO_ID = 0;
    const PROFILE_DATA_SQL_CUSTOMER =
        'SELECT
            JSON_OBJECT(
                "id_customer", c.`id_customer`,
                "id_shop", c.`id_shop`,
                "id_shop_group", c.`id_shop_group`,
                "optin", c.`optin`,
                "newsletter", c.`newsletter`,
                "newsletter_date_add", UNIX_TIMESTAMP(c.`newsletter_date_add`),
                "email", c.`email`,
                "firstname", c.`firstname`,
                "lastname", c.`lastname`,
                "birthday", UNIX_TIMESTAMP(c.`birthday`),
                "company", c.`company`,
                "date_add", UNIX_TIMESTAMP(c.`date_add`),
                "shop_name", s.`name`,
                "shop_group_name", sg.`name`,
                "language_name", l.`name`,
                "default_group_name", gl.`name`,
                "sales_columns", (
                    SELECT
                        JSON_OBJECT(
                            "lifetime_total_orders", COUNT(*),
                            "lifetime_total_spent", SUM(o.`total_paid_tax_incl`),
                            "average_order_value", SUM(o.`total_paid_tax_incl`) / COUNT(*)
                        )
                    FROM `' . _DB_PREFIX_ . 'orders` o
                    LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (o.`current_state` = os.`id_order_state`)
                    WHERE o.`id_customer` = c.`id_customer` AND o.`id_shop` = c.`id_shop` AND os.`invoice` = 1
                ),
                "order_address_ids", (
                    SELECT
                        JSON_OBJECT(
                            "id_address_invoice", o.`id_address_invoice`,
                            "id_address_delivery", o.`id_address_delivery`
                        )
                    FROM `' . _DB_PREFIX_ . 'orders` o
                    LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (o.`current_state` = os.`id_order_state`)
                    WHERE o.`id_customer` = c.`id_customer` AND o.`id_shop` = c.`id_shop` AND os.`invoice` = 1
                    ORDER BY o.`id_order` DESC
                    LIMIT 1
                ),
                "address_collection", (
                    SELECT
                        JSON_OBJECTAGG(
                            a.`id_address`, JSON_OBJECT(
                                "address1", a.`address1`,
                                "address2", a.`address2`,
                                "postcode", a.`postcode`,
                                "city", a.`city`,
                                "state", s.name,
                                "country", cl.`name`,
                                "country_code", c.`iso_code`,
                                "phone", a.`phone`,
                                "phone_mobile", a.`phone_mobile`
                            )
                        )
                    FROM `' . _DB_PREFIX_ . 'address` a
                    LEFT JOIN `' . _DB_PREFIX_ . 'country` c ON (c.`id_country` = a.`id_country`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (cl.`id_country` = a.`id_country`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'state` s ON (s.`id_state` = a.`id_state`)
                    WHERE cl.`id_lang` = c.`id_lang` AND a.`id_customer` = c.`id_customer` AND a.`deleted` = 0 AND a.`active` = 1
                )
            )
        FROM `' . _DB_PREFIX_ . 'customer` c
        LEFT JOIN `' . _DB_PREFIX_ . 'shop` s ON (c.`id_shop` = s.`id_shop`)
        LEFT JOIN `' . _DB_PREFIX_ . 'shop_group` sg ON (c.`id_shop_group` = sg.`id_shop_group`)
        LEFT JOIN `' . _DB_PREFIX_ . 'lang` l ON (c.`id_lang` = l.`id_lang`)
        LEFT JOIN `' . _DB_PREFIX_ . 'group_lang` gl ON (c.`id_default_group`, c.`id_lang`) = (gl.`id_group`, gl.`id_lang`)
        WHERE c.`id_customer` = %s';
    const PROFILE_DATA_SQL_SUBSCRIBER =
        'SELECT
            JSON_OBJECT(
                "id_subscriber", en.`id`,
                "newsletter", "1",
                "id_shop", en.`id_shop`,
                "id_shop_group", en.`id_shop_group`,
                "email", en.`email`,
                "newsletter_date_add", UNIX_TIMESTAMP(en.`newsletter_date_add`),
                "shop_name", s.`name`,
                "shop_group_name", sg.`name`,
                "language_name", l.`name`
            )
        FROM `' . _DB_PREFIX_ . 'emailsubscription` en
        LEFT JOIN `' . _DB_PREFIX_ . 'shop` s ON (en.`id_shop` = s.`id_shop`)
        LEFT JOIN `' . _DB_PREFIX_ . 'shop_group` sg ON (en.`id_shop_group` = sg.`id_shop_group`)
        LEFT JOIN `' . _DB_PREFIX_ . 'lang` l ON (en.`id_lang` = l.`id_lang`)
        WHERE en.`id` = %s';

    /**
     * @return string
     */
    public static function getRepositoryClassName(): string;
}
