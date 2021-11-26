<?php

namespace Apsis\One\Controller\Admin;

use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Apsis\One\Helper\HelperInterface as HI;
use Apsis\One\Model\EntityInterface as EI;

interface ControllerInterface extends ContainerAwareInterface
{
    const HELP_LINK = 'https://help.apsis.com/en';

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
     * @param string $redirectRoute
     */
    public function __construct(GridFactoryInterface $gridFactory, string $redirectRoute);
}
