<?php

namespace Apsis\One\Module;

use Apsis_one;
use Apsis\One\Model\EntityInterface as EI;

interface SetupInterface
{
    /** READ ONLY FIELD KEYS  */
    const READ_ONLY_FIELD_ACCOUNT_STATUS = 'APSIS_ONE_ACCOUNT_STATUS';
    const READ_ONLY_FILED_BASE_URL = 'APSIS_ONE_BASE_URL';

    const API_INSTALL_CONFIG_CONTROLLER = 'apiinstallationconfig';
    const API_STORES_CONTROLLER = 'apistores';
    const API_PROFILES_CONTROLLER = 'apiprofiles';
    const API_PROFILE_UPDATE_CONTROLLER = 'apisubscriptionupdate';

    /** CONFIGURATION PREFIX  */
    const CONFIG_PREFIX = 'APSIS_ONE_';

    /** CONFIGURATION KEYS  */
    const CONFIG_KEY_GLOBAL_KEY = self::CONFIG_PREFIX . 'GLOBAL_KEY';
    const CONFIG_KEY_PROFILE_SYNC_FLAG = self::CONFIG_PREFIX . 'PROFILE_SYNC_ENABLED';
    const CONFIG_KEY_EVENT_SYNC_FLAG = self::CONFIG_PREFIX . 'EVENT_SYNC_ENABLED';
    const CONFIG_KEY_TRACKING_CODE = self::CONFIG_PREFIX . 'TRACKING_CODE';
    const CONFIG_KEY_INSTALLATION_CONFIGS = self::CONFIG_PREFIX . 'INSTALLATION_CONFIGS';
    const CONFIG_KEY_API_TOKEN = self::CONFIG_PREFIX . 'API_TOKEN';
    const CONFIG_KEY_API_TOKEN_EXPIRY = self::CONFIG_PREFIX . 'API_TOKEN_EXPIRY';
    const CONFIG_KEY_PROFILE_SYNC_SIZE = self::CONFIG_PREFIX . 'PROFILE_SYNC_SIZE';

    /** INSTALLATION CONFIGURATION KEYS  */
    const INSTALLATION_CONFIG_CLIENT_ID = 'client_id';
    const INSTALLATION_CONFIG_CLIENT_SECRET = 'client_secret';
    const INSTALLATION_CONFIG_SECTION_DISCRIMINATOR = 'section_discriminator';
    const INSTALLATION_CONFIG_KEYSPACE_DISCRIMINATOR = 'keyspace_discriminator';
    const INSTALLATION_CONFIG_API_BASE_URL = 'api_base_url';

    /** LIST KEYS CLIENT CONFIGS  */
    const CLIENT_CONFIGS = [
        self::INSTALLATION_CONFIG_CLIENT_ID,
        self::INSTALLATION_CONFIG_CLIENT_SECRET,
        self::INSTALLATION_CONFIG_API_BASE_URL
    ];

    /** CONFIG FLAGS */
    const FLAG_YES = 1;
    const FLAG_NO = 0;

    /** LIMITATIONS  */
    const DEFAULT_SYNC_SIZE = 5000;
    const DEFAULT_DB_CLEANUP_AFTER = 30; //Days

    /** MODULE */
    const MODULE_NAME = 'apsis_one';
    const MODULE_DISPLAY_NAME = 'APSIS One';
    const MODULE_VERSION  = '1.0.0';
    const MODULE_CONFIG_TAB = 'Configure Module';

    /** CLASS NAMES FOR LEGACY USAGE */
    const APSIS_MENU = 'AdminParentApsis';
    const APSIS_CONFIG_TAB = 'ApsisOneModuleConfig';
    const LEGACY_CONTROLLER_CLASSES = [
        self::APSIS_MENU => self::APSIS_MENU,
        EI::T_PROFILE => 'ApsisOneProfileController',
        EI::T_EVENT => 'ApsisOneEventController',
        EI::T_ABANDONED_CART => 'ApsisOneAbandonedCartController',
        self::APSIS_CONFIG_TAB => 'ApsisOneModuleConfigController'
    ];

    const PS_T_WISHLIST_PRODUCT = 'wishlist_product';
    const PS_T_PRODUCT_COMMENT = 'product_comment';
    const PS_T_ORDERS = 'orders';
    const PS_T_CUSTOMER = 'customer';
    const PS_T_NEWSLETTER = 'emailsubscription';
    const PS_T_CUSTOMER_ALIAS = 'pc';
    const PS_T_NEWSLETTER_ALIAS = 'pes';
    const T_DEF_VALUES = 'default';
    const PS_COLUMNS_SEL = [
        self::T_DEF_VALUES => [
            EI::C_ID_INTEGRATION => '(SELECT UUID()) as `%s`',
            EI::C_ID_SHOP => '`%s`',
            EI::C_EMAIL => '`%s`',
            EI::C_SYNC_STATUS => EI::SS_JUSTIN . ' as `%s`',
            EI::C_ERROR_MSG => '"" as `%s`',
            EI::C_DATE_UPD => '(SELECT NOW()) as `%s`',
        ],
        self::PS_T_CUSTOMER => [
            EI::C_ID_CUSTOMER => '`%s`',
            EI::C_ID_NEWSLETTER => EI::NO_ID . ' AS `%s`',
            EI::C_IS_CUSTOMER => self::FLAG_YES . ' AS `%s`',
            EI::C_IS_GUEST => '`%s`',
            EI::C_IS_NEWSLETTER => '`newsletter` AS `%s`',
            EI::C_IS_OFFERS => '`optin` AS `%s`',
            EI::C_PROFILE_DATA => '(' . EI::PROFILE_DATA_SQL_CUSTOMER .') AS `%s`'
        ],
        self::PS_T_NEWSLETTER => [
            EI::C_ID_CUSTOMER => self::FLAG_NO . ' AS `%s`',
            EI::C_ID_NEWSLETTER => '`id` AS `%s`',
            EI::C_IS_CUSTOMER => self::FLAG_NO . ' AS `%s`',
            EI::C_IS_GUEST => self::FLAG_NO . ' AS `%s`',
            EI::C_IS_NEWSLETTER => self::FLAG_YES . ' AS `%s`',
            EI::C_IS_OFFERS => self::FLAG_NO . ' AS `%s`',
            EI::C_PROFILE_DATA => '(' . EI::PROFILE_DATA_SQL_SUBSCRIBER . ') AS `%s`'
        ]
    ];
    const PS_WHERE_COND = [
        self::T_DEF_VALUES => ['`email` != ""', '`email` IS NOT NULL'],
        self::PS_T_CUSTOMER => ['`deleted` = ' . self::FLAG_NO, '`active` = ' . self::FLAG_YES],
        self::PS_T_NEWSLETTER => ['`active` = ' . self::FLAG_YES]
    ];

    const T_PROFILE_MIGRATE_DATA_FROM_TABLES = [
        self::PS_T_CUSTOMER => self::PS_T_CUSTOMER_ALIAS,
        self::PS_T_NEWSLETTER => self::PS_T_NEWSLETTER_ALIAS
    ];

    const T_EVENT_MIGRATE_HISTORICAL_EVENTS_SQL = [
        self::PS_T_WISHLIST_PRODUCT => EI::EVENT_DATA_SQL_WISHLIST_PRODUCT,
        self::PS_T_PRODUCT_COMMENT => EI::EVENT_DATA_SQL_REVIEW_PRODUCT,
        self::PS_T_ORDERS => EI::EVENT_DATA_SQL_ORDER
    ];

    /**
     * @param Apsis_one $module
     *
     * @return bool|string|void
     */
     public function init(Apsis_one $module);
}
