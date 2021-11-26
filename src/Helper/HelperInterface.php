<?php

namespace Apsis\One\Helper;

use Throwable;

interface HelperInterface
{
    /** DATE FORMATS */
    const TIMESTAMP = 'U';
    const ISO_8601 = 'c';

    /** CUSTOMER ENTITY HOOKS  */
    const CUSTOMER_HOOK_ADD_AFTER = 'actionObjectCustomerAddAfter';
    const CUSTOMER_HOOK_UPDATE_AFTER = 'actionObjectCustomerUpdateAfter';
    const CUSTOMER_HOOK_DELETE_AFTER = 'actionObjectCustomerDeleteAfter';
    const CUSTOMER_HOOK_AUTH = 'actionAuthentication';
    const CUSTOMER_HOOK_DISPLAY_ACCOUNT = 'displayCustomerAccount';
    const DISPLAY_AFTER_BODY = 'displayAfterBodyOpeningTag';
    const CUSTOMER_HOOKS = [
        self::CUSTOMER_HOOK_ADD_AFTER,
        self::CUSTOMER_HOOK_UPDATE_AFTER,
        self::CUSTOMER_HOOK_DELETE_AFTER,
        self::CUSTOMER_HOOK_AUTH
    ];

    /** SUBSCRIPTION ENTITY HOOKS  */
    const EMAIL_SUBSCRIPTION_HOOK_REGISTER_AFTER = 'actionNewsletterRegistrationAfter';
    const EMAIL_SUBSCRIPTION_HOOKS = [self::EMAIL_SUBSCRIPTION_HOOK_REGISTER_AFTER];

    /** ADDRESS ENTITY HOOKS  */
    const ADDRESS_HOOK_ADD_AFTER = 'actionObjectAddressAddAfter';
    const ADDRESS_HOOK_UPDATE_AFTER = 'actionObjectAddressUpdateAfter';
    const ADDRESS_HOOK_DELETE_AFTER = 'actionObjectAddressDeleteAfter';
    const ENTITY_ADDRESS_HOOKS = [
        self::ADDRESS_HOOK_ADD_AFTER,
        self::ADDRESS_HOOK_UPDATE_AFTER,
        self::ADDRESS_HOOK_DELETE_AFTER
    ];

    /** PRODUCT COMMENT ENTITY HOOKS  */
    const PRODUCT_COMMENT_HOOK_VALIDATE = 'actionObjectProductCommentValidateAfter';
    const PRODUCT_COMMENT_HOOKS = [self::PRODUCT_COMMENT_HOOK_VALIDATE];

    /** WISHLIST ENTITY HOOKS  */
    const WISHLIST_HOOK_ADD_PRODUCT = 'actionWishlistAddProduct';
    const WISHLIST_HOOKS = [self::WISHLIST_HOOK_ADD_PRODUCT];

    /** ORDER ENTITY HOOKS  */
    const ORDER_HOOK_ADD_AFTER = 'actionObjectOrderAddAfter';
    const ORDER_HOOK_UPDATE_AFTER = 'actionObjectOrderUpdateAfter';
    const ORDER_HOOKS = [self::ORDER_HOOK_ADD_AFTER, self::ORDER_HOOK_UPDATE_AFTER];

    /** CART ENTITY HOOKS  */
    const CART_HOOK_UPDATE_QTY_BEFORE = 'actionCartUpdateQuantityBefore';
    const CART_HOOKS = [self::CART_HOOK_UPDATE_QTY_BEFORE];

    /**
     * SERVICES
     */
    /** MODULE */
    const SERVICE_MODULE = 'apsis_one.module';
    const SERVICE_MODULE_INSTALL = 'apsis_one.module.install';
    const SERVICE_MODULE_UNINSTALL = 'apsis_one.module.uninstall';
    const SERVICE_MODULE_CONFIGS = 'apsis_one.module.configuration.configs';
    const SERVICE_MODULE_API_CLIENT_FACTORY = 'apsis_one.module.api.clientfactory';
    const SERVICE_MODULE_ADMIN_CONFIGURATION = 'apsis_one.module.configuration';
    const SERVICE_MODULE_HOOK_PROCESSOR = 'apsis_one.module.hook-processor';
    /** CONTEXT */
    const SERVICE_CONTEXT_SHOP = 'apsis_one.context.shop';
    const SERVICE_CONTEXT_LINK = 'apsis_one.context.link';
    /** HELPER */
    const SERVICE_HELPER_LOGGER = 'apsis_one.helper.logger';
    const SERVICE_HELPER_DATE = 'apsis_one.helper.date';
    const SERVICE_HELPER_MODULE = 'apsis_one.helper.module';
    const SERVICE_HELPER_ENTITY = 'apsis_one.helper.entity';
    /** PROFILE */
    const SERVICE_PROFILE_SCHEMA = 'apsis_one.profile.schema';
    const SERVICE_PROFILE_CONTAINER = 'apsis_one.profile.container';
    /** ABANDONED CART */
    const SERVICE_ABANDONED_CART_SCHEMA = 'apsis_one.abandoned-cart.schema';
    const SERVICE_ABANDONED_CART_ITEM_SCHEMA = 'apsis_one.abandoned-cart-item.schema';
    const SERVICE_ABANDONED_CART_CONTAINER = 'apsis_one.abandoned-cart.container';
    /** EVENT */
    const SERVICE_EVENT_CONTAINER = 'apsis_one.event.container';
    const SERVICE_EVENT_CUSTOMER_OPTIN_NEWSLETTER_SCHEMA = 'apsis_one.event.customer-optin-newsletter.schema';
    const SERVICE_EVENT_CUSTOMER_OPTOUT_NEWSLETTER_SCHEMA = 'apsis_one.event.customer-optout-newsletter.schema';
    const SERVICE_EVENT_CUSTOMER_OPTIN_OFFERS_SCHEMA = 'apsis_one.event.customer-optin-offers.schema';
    const SERVICE_EVENT_CUSTOMER_OPTOUT_OFFERS_SCHEMA = 'apsis_one.event.customer-optout-offers.schema';
    const SERVICE_EVENT_NEWSLETTER_SUB_IS_CUSTOMER_SCHEMA = 'apsis_one.event.newsletter-subscriber-is-customer.schema';
    const SERVICE_EVENT_NEWSLETTER_GUEST_OPTIN_SCHEMA = 'apsis_one.event.newsletter-guest-optin.schema';
    const SERVICE_EVENT_NEWSLETTER_GUEST_OPTOUT_SCHEMA = 'apsis_one.event.newsletter-guest-optin.schema';
    const SERVICE_EVENT_CUSTOMER_LOGIN_SCHEMA = 'apsis_one.event.customer-login.schema';
    const SERVICE_EVENT_CUSTOMER_PRODUCT_WISHED_SCHEMA = 'apsis_one.event.product-wished.schema';
    const SERVICE_EVENT_COMMON_PRODUCT_CARTED_SCHEMA = 'apsis_one.event.product-carted.schema';
    const SERVICE_EVENT_COMMON_PRODUCT_REVIEWED_SCHEMA = 'apsis_one.event.product-reviewed.schema';
    const SERVICE_EVENT_COMMON_ORDER_PLACED_SCHEMA = 'apsis_one.event.order-placed.schema';
    const SERVICE_EVENT_COMMON_ORDER_PLACED_PRODUCT_SCHEMA = 'apsis_one.event.order-placed-product.schema';
    /** OTHERS */
    const SERVICE_PS_LEGACY_CONTEXT_LOADER = 'prestashop.adapter.legacy_context_loader';

    /**
     * ROUTES
     */
    /** GRID  */
    const GRID_ROUTE_PROFILE_LIST = 'admin_apsis_profile_index';
    const GRID_ROUTE_EVENT_LIST = 'admin_apsis_event_index';
    const GRID_ROUTE_AC_LIST = 'admin_apsis_abandonedcart_index';
    const GRID_ROUTE_PROFILE_RESET = 'admin_apsis_profile_reset';
    const GRID_ROUTE_EVENT_RESET = 'admin_apsis_event_reset';
    const GRID_ROUTE_AC_RESET = 'admin_apsis_abandonedcart_reset';
    const GRID_ROUTE_PROFILE_DELETE = 'admin_apsis_profile_delete';
    const GRID_ROUTE_EVENT_DELETE = 'admin_apsis_event_delete';
    const GRID_ROUTE_AC_DELETE = 'admin_apsis_abandonedcart_delete';
    const GRID_ROUTE_PROFILE_RESET_BULK = 'admin_apsis_profile_reset_bulk';
    const GRID_ROUTE_EVENT_RESET_BULK = 'admin_apsis_event_reset_bulk';
    const GRID_ROUTE_AC_RESET_BULK = 'admin_apsis_abandonedcart_reset_bulk';
    const GRID_ROUTE_PROFILE_DELETE_BULK = 'admin_apsis_profile_delete_bulk';
    const GRID_ROUTE_EVENT_DELETE_BULK = 'admin_apsis_event_delete_bulk';
    const GRID_ROUTE_AC_DELETE_BULK = 'admin_apsis_abandonedcart_delete_bulk';
    const GRID_ROUTE_PROFILE_EXPORT = 'admin_apsis_profile_export';
    const GRID_ROUTE_EVENT_EXPORT = 'admin_apsis_event_export';
    const GRID_ROUTE_AC_EXPORT = 'admin_apsis_abandonedcart_export';
    /** OTHER  */
    const MODULE_CONFIG_ROUTE = 'admin_apsis_module_config';
    const MODULE_LOG_VIEWER_ROUTE = 'admin_apsis_module_log_viewer';

    /** TEMPLATES */
    const TPL_BASE_PATH = '@Modules/apsis_one/views/templates/admin/grids/';
    const TPL_PROFILE_LIST = self::TPL_BASE_PATH . '/profile_list.html.twig';
    const TPL_EVENT_LIST = self::TPL_BASE_PATH . '/event_list.html.twig';
    const TPL_AC_LIST = self::TPL_BASE_PATH . '/ac_list.html.twig';

    /** COMMANDS */
    const SERVICE_COMMAND_SYNC = 'apsis_one.command.sync';
    const SERVICE_COMMAND_DB = 'apsis_one.command.db';

    /** SERVICE CONTAINERS  */
    const FROM_CONTAINER_MS = 'getModuleSpecificContainer';
    const FROM_CONTAINER_FD = 'getFromContainerFinderAdapter';
    const FROM_CONTAINER_SA = 'getFromSymfonyContainerAdapter';
    const CONTAINER_RELATIONS = [
        self::FROM_CONTAINER_MS => self::FROM_CONTAINER_FD,
        self::FROM_CONTAINER_FD => self::FROM_CONTAINER_SA,
        self::FROM_CONTAINER_SA => ''
    ];

    const APSIS_WEB_COOKIE_NAME = 'Ely_vID';
    const APSIS_WEB_COOKIE_DURATION = 31536000; // 1 Year

    /**
     * @param string $message
     *
     * @return void
     */
    public function logInfoMsg(string $message): void;

    /**
     * @param string $message
     * @param array $info
     *
     * @return void
     */
    public function logDebugMsg(string $message, array $info): void;

    /**
     * Log an error message.
     *
     * @param string $message
     * @param Throwable $e
     *
     * @return void
     */
    public function logErrorMsg(string $message, Throwable $e): void;
}
