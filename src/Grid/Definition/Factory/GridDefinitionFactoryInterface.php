<?php

namespace Apsis\One\Grid\Definition\Factory;

use Apsis\One\Helper\HelperInterface as HI;
use Apsis\One\Model\AbandonedCart;
use Apsis\One\Model\Event;
use Apsis\One\Model\Profile;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\FilterableGridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Apsis\One\Form\ChoiceProvider\ProviderInterface;
use Apsis\One\Model\EntityInterface as EI;

interface GridDefinitionFactoryInterface extends FilterableGridDefinitionFactoryInterface
{
    /** ROUTES MAP */
    const GRID_ROUTES_LIST_MAP = [
        EI::T_PROFILE => HI::GRID_ROUTE_PROFILE_LIST,
        EI::T_EVENT => HI::GRID_ROUTE_EVENT_LIST,
        EI::T_ABANDONED_CART => HI::GRID_ROUTE_AC_LIST
    ];
    const GRID_ROUTES_RESET_MAP = [
        EI::T_PROFILE => HI::GRID_ROUTE_PROFILE_RESET,
        EI::T_EVENT => HI::GRID_ROUTE_EVENT_RESET,
        EI::T_ABANDONED_CART => HI::GRID_ROUTE_AC_RESET
    ];
    const GRID_ROUTES_DELETE_MAP = [
        EI::T_PROFILE => HI::GRID_ROUTE_PROFILE_DELETE,
        EI::T_EVENT => HI::GRID_ROUTE_EVENT_DELETE,
        EI::T_ABANDONED_CART => HI::GRID_ROUTE_AC_DELETE
    ];
    const GRID_ROUTES_RESET_BULK_MAP = [
        EI::T_PROFILE => HI::GRID_ROUTE_PROFILE_RESET_BULK,
        EI::T_EVENT => HI::GRID_ROUTE_EVENT_RESET_BULK,
        EI::T_ABANDONED_CART => HI::GRID_ROUTE_AC_RESET_BULK
    ];
    const GRID_ROUTES_DELETE_BULK_MAP = [
        EI::T_PROFILE => HI::GRID_ROUTE_PROFILE_DELETE_BULK,
        EI::T_EVENT => HI::GRID_ROUTE_EVENT_DELETE_BULK,
        EI::T_ABANDONED_CART => HI::GRID_ROUTE_AC_DELETE_BULK
    ];
    const GRID_ROUTES_EXPORT_MAP = [
        EI::T_PROFILE => HI::GRID_ROUTE_PROFILE_EXPORT,
        EI::T_EVENT => HI::GRID_ROUTE_EVENT_EXPORT,
        EI::T_ABANDONED_CART => HI::GRID_ROUTE_AC_EXPORT
    ];
    const GRID_ENTITY_CLASSNAME_MAP = [
        EI::T_PROFILE => Profile::class,
        EI::T_EVENT => Event::class,
        EI::T_ABANDONED_CART => AbandonedCart::class
    ];

    /** GRID COLUMNS */
    const COLUMN_TYPE_ACTIONS = 'actions';
    const COLUMN_TYPE_BULK_ACTION = 'bulk_action';
    const FILTER_TYPE_MAPPINGS = [
        'isUnsignedId' => NumberType::class,
        'isInt' => ChoiceType::class,
        'isBool' => YesAndNoChoiceType::class,
        'isString' => TextType::class,
        'isEmail' => TextType::class,
        'isJson' => TextType::class,
        'isDate' => DateRangeType::class
    ];
    const BOOLEAN_COLUMNS = [
        EI::C_IS_NEWSLETTER,
        EI::C_IS_OFFERS,
        EI::C_IS_GUEST,
        EI::C_IS_CUSTOMER
    ];
    const NOT_ALLOWED_COLUMNS = [
        EI::C_ID_ENTITY_PS,
        EI::C_PROFILE_DATA,
        EI::C_EVENT_DATA,
        EI::C_SUB_EVENT_DATA,
        EI::C_CART_DATA
    ];

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param ProviderInterface|null $syncStatusProvider
     * @param ProviderInterface|null $eventTypeProvider
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        ?ProviderInterface $syncStatusProvider = null,
        ?ProviderInterface $eventTypeProvider = null
    );

    /**
     * @return string
     */
    public function getFilterId(): string;

    /**
     * @param string $gridId
     *
     * @return array
     */
    public static function getAllowedGridColumns(string $gridId): array;

    /**
     * @param string $gridId
     *
     * @return array
     */
    public static function getAllowedGridFilters(string $gridId): array;
}
