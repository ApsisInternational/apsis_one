<?php

namespace Apsis\One\Helper;

use Throwable;

interface HelperInterface
{
    /** DATE FORMATS */
    const TIMESTAMP = 'U';
    const ISO_8601 = 'c';

    /** CUSTOMER ENTITY HOOKS  */
    const ENTITY_CUSTOMER_HOOKS = [
        'actionObjectCustomerAddAfter',
        'actionObjectCustomerUpdateAfter',
        'actionObjectCustomerDeleteAfter',
        'actionAuthentication',
        'displayCustomerAccount'
    ];

    /** SUBSCRIPTION ENTITY HOOKS  */
    const ENTITY_SUBSCRIPTION_HOOKS = [
        'actionNewsletterRegistrationAfter'
    ];

    /** ADDRESS ENTITY HOOKS  */
    const ENTITY_ADDRESS_HOOKS = [];

    /** GDPR ENTITY HOOKS  */
    const ENTITY_GDPR_HOOKS = [];

    /** PRODUCT COMMENT ENTITY HOOKS  */
    const ENTITY_PRODUCT_COMMENT_HOOKS = ['actionObjectProductCommentValidateAfter'];

    /** WISHLIST ENTITY HOOKS  */
    const ENTITY_WISHLIST_HOOKS = ['actionWishlistAddProduct'];

    /** ORDER ENTITY HOOKS  */
    const ENTITY_ORDER_HOOKS = ['actionValidateOrder', 'actionObjectOrderAddAfter'];

    /** CART ENTITY HOOKS  */
    const ENTITY_CART_HOOKS = [ 'actionCartUpdateQuantityBefore'];

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
    const SERVICE_HELPER_DATA = 'apsis_one.helper.data';
    const SERVICE_HELPER_DATE = 'apsis_one.helper.date';
    const SERVICE_HELPER_MODULE = 'apsis_one.helper.module';

    /** PROFILE */
    const SERVICE_PROFILE_SCHEMA = 'apsis_one.profile.schema';
    const SERVICE_PROFILE_CONTAINER = 'apsis_one.profile.container';
    const SERVICE_PROFILE_REPOSITORY = 'apsis_one.profile.repository';

    /** ABANDONED CART */
    const SERVICE_ABANDONED_CART_SCHEMA = 'apsis_one.abandoned-cart.schema';
    const SERVICE_ABANDONED_CART_ITEM_SCHEMA = 'apsis_one.abandoned-cart-item.schema';
    const SERVICE_ABANDONED_CART_CONTAINER = 'apsis_one.abandoned-cart.container';
    const SERVICE_ABANDONED_CART_REPOSITORY = 'apsis_one.abandoned-cart.repository';

    /** EVENT */
    const SERVICE_EVENT_CONTAINER = 'apsis_one.event.container';
    const SERVICE_EVENT_REPOSITORY = 'apsis_one.event.repository.';
    const SERVICE_EVENT_CUSTOMER_IS_SUBSCRIBER_SCHEMA = 'apsis_one.event.customer-is-subscriber.schema';
    const SERVICE_EVENT_CUSTOMER_LOGIN_SCHEMA = 'apsis_one.event.customer-login.schema';
    const SERVICE_EVENT_CUSTOMER_PRODUCT_WISHED_SCHEMA = 'apsis_one.event.product-wished.schema';
    const SERVICE_EVENT_GUEST_IS_CUSTOMER_SCHEMA = 'apsis_one.event.guest-is-customer.schema';
    const SERVICE_EVENT_GUEST_IS_SUBSCRIBER_SCHEMA = 'apsis_one.event.guest-is-subscriber.schema';
    const SERVICE_EVENT_SUBSCRIBER_IS_GUEST_SCHEMA = 'apsis_one.event.subscriber-is-guest.schema';
    const SERVICE_EVENT_SUBSCRIBER_IS_CUSTOMER_SCHEMA = 'apsis_one.event.subscriber-is-customer.schema';
    const SERVICE_EVENT_SUBSCRIBER_UNSUBSCRIBE_SCHEMA = 'apsis_one.event.subscriber-unsubscribe.schema';
    const SERVICE_EVENT_COMMON_PRODUCT_CARTED_SCHEMA = 'apsis_one.event.product-carted.schema';
    const SERVICE_EVENT_COMMON_PRODUCT_REVIEWED_SCHEMA = 'apsis_one.event.product-reviewed.schema';
    const SERVICE_EVENT_COMMON_ORDER_PLACED_SCHEMA = 'apsis_one.event.cart-order-placed.schema';
    const SERVICE_EVENT_COMMON_ORDER_PLACED_PRODUCT_SCHEMA = 'apsis_one.event.cart-order-placed-product.schema';
    const SERVICE_EVENT_COMMON_CART_ABANDONED_SCHEMA = 'apsis_one.event.cart-abandoned.schema';
    const SERVICE_EVENT_COMMON_CART_ABANDONED_PRODUCT_SCHEMA = 'apsis_one.event.cart-abandoned-product.schema';

    /** GRID */
    const SERVICE_PROFILE_GRID_FACTORY_ID = 'apsis_one.grid.profile_grid_factory';
    const SERVICE_EVENT_GRID_FACTORY_ID = 'apsis_one.grid.event_grid_factory';
    const SERVICE_AC_GRID_FACTORY_ID = 'apsis_one.grid.abandonedcart_grid_factory';
    const SERVICE_PROFILE_GRID_DEF_FACTORY_ID = 'apsis_one.grid.definition.factory.profile_grid_definition_factory';
    const SERVICE_EVENT_GRID_DEF_FACTORY_ID = 'apsis_one.grid.definition.factory.event_grid_definition_factory';
    const SERVICE_AC_GRID_DEF_FACTORY_ID = 'apsis_one.grid.definition.factory.abandonedcart_grid_definition_factory';

    /** GRID ROUTES  */
    const GRID_ROUTE_PROFILE_LIST = 'admin_apsis_profiles_index';
    const GRID_ROUTE_EVENT_LIST = 'admin_apsis_events_index';
    const GRID_ROUTE_AC_LIST = 'admin_apsis_abandonedcart_index';
    const GRID_ROUTE_PROFILE_RESET = 'admin_apsis_profiles_reset';
    const GRID_ROUTE_EVENT_RESET = 'admin_apsis_event_reset';
    const GRID_ROUTE_AC_RESET = 'admin_apsis_abandonedcart_reset';
    const GRID_ROUTE_PROFILE_DELETE = 'admin_apsis_profiles_delete';
    const GRID_ROUTE_EVENT_DELETE = 'admin_apsis_event_delete';
    const GRID_ROUTE_AC_DELETE = 'admin_apsis_abandonedcart_delete';

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