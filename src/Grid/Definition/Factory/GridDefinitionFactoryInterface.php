<?php

namespace Apsis\One\Grid\Definition\Factory;

use Apsis\One\Helper\HelperInterface as HI;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface as PsGridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Apsis\One\Form\ChoiceProvider\ProviderInterface;
use Apsis\One\Entity\EntityInterface as EI;

interface GridDefinitionFactoryInterface extends PsGridDefinitionFactoryInterface
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
    const GRID_ROUTES_DELETE = [
        EI::T_PROFILE => HI::GRID_ROUTE_PROFILE_DELETE,
        EI::T_EVENT => HI::GRID_ROUTE_EVENT_DELETE,
        EI::T_ABANDONED_CART => HI::GRID_ROUTE_AC_DELETE
    ];

    /** GRID COLUMNS RELATED  */
    const COLUMN_TYPE_ACTIONS = 'actions';
    const COLUMN_TYPE_BULK_ACTION = 's_bulk';
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
        EI::C_IS_SUBSCRIBER,
        EI::C_IS_GUEST,
        EI::C_IS_CUSTOMER
    ];

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param string $resetActionUrl
     * @param string $redirectionUrl
     * @param ProviderInterface|null $provider
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        string $resetActionUrl,
        string $redirectionUrl,
        ?ProviderInterface $provider = null
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