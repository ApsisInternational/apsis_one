<?php

namespace Apsis\One\Module;

use Apsis_one;
use Apsis\One\Entity\EntityInterface as EI;

interface SetupInterface
{
    /** READ ONLY FIELD KEYS  */
    const READ_ONLY_FIELD_ACCOUNT_STATUS = 'APSIS_ONE_ACCOUNT_STATUS';
    const READ_ONLY_FILED_BASE_URL = 'APSIS_ONE_BASE_URL';

    const API_STORES_CONTROLLER_FILENAME = 'apistores';

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
    const CONFIG_KEY_DB_CLEANUP_AFTER = self::CONFIG_PREFIX . 'DB_CLEANUP_AFTER';

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
    const CONFIG_FLAG_YES = 1;
    const CONFIG_FLAG_NO = 0;

    /** LIMITATIONS  */
    const DEFAULT_SYNC_SIZE = 5000;
    const DEFAULT_DB_CLEANUP_AFTER = 30; //Days

    /** MODULE */
    const MODULE_NAME = 'apsis_one';
    const MODULE_DISPLAY_NAME = 'APSIS One';
    const MODULE_VERSION  = '1.0.0';

    /** CLASS NAMES FOR LEGACY USAGE */
    const APSIS_MENU = 'AdminParentApsis';
    const LEGACY_CONTROLLER_CLASSES = [
        self::APSIS_MENU => self::APSIS_MENU,
        EI::T_PROFILE => 'ApsisOneProfileController',
        EI::T_EVENT => 'ApsisOneEventController',
        EI::T_ABANDONED_CART => 'ApsisOneAbandonedCartController'
    ];

    /**
     * @param Apsis_one $module
     *
     * @return bool|string|void
     */
     public function init(Apsis_one $module);
}
