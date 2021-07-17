<?php

namespace Apsis\One\Controller\Admin;

use Apsis\One\Grid\Search\Filters\EventFilters;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PrestaShopBundle\Security\Annotation\AdminSecurity;

class EventController extends AbstractController
{
    /**
     * @AdminSecurity("is_granted(['read', 'create', 'update', 'delete'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param EventFilters $filter
     *
     * @return Response
     */
    public function indexAction(Request $request, EventFilters $filter): Response
    {
        return $this->processList($request, $filter);
    }
}