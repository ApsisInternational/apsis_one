<?php

namespace Apsis\One\Controller\Admin;

use PrestaShop\PrestaShop\Core\Grid\Filter\GridFilterFormFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Apsis\One\Helper\HelperInterface as HI;
use Apsis\One\Entity\EntityInterface as EI;

interface ControllerInterface extends ContainerAwareInterface
{
    /** MAPPINGS */
    const TEMPLATES = [
        EI::T_PROFILE => HI::TPL_PROFILE_LIST,
        EI::T_EVENT => HI::TPL_EVENT_LIST,
        EI::T_ABANDONED_CART => HI::TPL_AC_LIST,
    ];

    /**
     * Class constructor.
     *
     * @param GridFactoryInterface $gridFactory
     * @param GridFilterFormFactoryInterface $filterFormFactory
     * @param string $redirectRoute
     */
    public function __construct(
        GridFactoryInterface $gridFactory,
        GridFilterFormFactoryInterface $filterFormFactory,
        string $redirectRoute
    );
}