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
    const API_CART_REBUILD_CONTROLLER = 'apicartrebuild';

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

    /** MODULE */
    const MODULE_NAME = 'apsis_one';
    const MODULE_AUTHOR = 'APSIS';
    const MODULE_TAB = 'advertising_marketing';
    const MODULE_DISPLAY_NAME = 'APSIS One';
    const MODULE_DESCRIPTION = 'Grow faster with the all-in-One marketing platform.';
    const MODULE_MSG_UNINSTALL = 'Are you sure you want to uninstall?';
    const MODULE_VERSION  = '1.0.0';
    const MODULE_CONFIG_TAB = 'Configure Module';
    const MODULE_LOG_VIEWER_TAB = 'Log Viewer';

    /** CLASS NAMES FOR LEGACY USAGE */
    const APSIS_MENU = 'AdminParentApsis';
    const APSIS_CONFIG_TAB = 'ApsisOneModuleConfig';
    const APSIS_LOGS_TAB = 'ApsisOneLogViewer';
    const LEGACY_CONTROLLER_CLASSES = [
        self::APSIS_MENU => self::APSIS_MENU,
        EI::T_PROFILE => 'ApsisOneProfileController',
        EI::T_EVENT => 'ApsisOneEventController',
        EI::T_ABANDONED_CART => 'ApsisOneAbandonedCartController',
        self::APSIS_CONFIG_TAB => 'ApsisOneModuleConfigController',
        self::APSIS_LOGS_TAB => 'ApsisOneLogViewerController'
    ];

    const T_CUSTOMER = 'customer';
    const T_SUBSCRIBER = 'emailsubscriber';
    const T_PROFILE_MIGRATE_DATA_FROM_TABLES = [
        self::T_CUSTOMER => EI::PROFILE_CUSTOMER_SQL_INSERT,
        self::T_SUBSCRIBER => EI::PROFILE_EMAIL_SUBSCRIBER_SQL_INSERT
    ];
    const T_DATE_COLUMN_MAP = [
        self::T_CUSTOMER => 'date_add',
        self::T_SUBSCRIBER => 'newsletter_date_add'
    ];

    const T_EVENT_MIGRATE_HISTORICAL_EVENTS_SQL = [
        EI::EVENT_WISHLIST_PRODUCT_SQL,
        EI::EVENT_REVIEW_PRODUCT_SQL,
        EI::EVENT_ORDER_INSERT_SQL
    ];

    /**
     * @param Apsis_one $module
     *
     * @return bool|string|void
     */
     public function init(Apsis_one $module);
}
