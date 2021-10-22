<?php

namespace Apsis\One\Controller\Admin;

use Apsis\One\Grid\Search\Filters\EventFilters;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PrestaShopBundle\Security\Annotation\AdminSecurity;

class EventController extends AbstractController
{
    /**
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))", message="Access denied.")
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

    /**
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param EventFilters $filter
     *
     * @return Response
     */
    public function exportAction(Request $request, EventFilters $filter): Response
    {
        return $this->processExport($request, $filter);
    }

    /**
     *
     * @AdminSecurity("is_granted(['delete'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param EventFilters $filter
     *
     * @return Response
     */
    public function deleteAction(Request $request, EventFilters $filter): Response
    {
        return parent::processDelete($request, $filter);
    }

    /**
     *
     * @AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param EventFilters $filter
     *
     * @return Response
     */
    public function resetAction(Request $request, EventFilters $filter): Response
    {
        return parent::processReset($request, $filter);
    }

    /**
     *
     * @AdminSecurity("is_granted(['delete'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param EventFilters $filter
     *
     * @return Response
     */
    public function deleteBulkAction(Request $request, EventFilters $filter): Response
    {
        return parent::processDeleteBulk($request, $filter);
    }

    /**
     *
     * @AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param EventFilters $filter
     *
     * @return Response
     */
    public function resetBulkAction(Request $request, EventFilters $filter): Response
    {
        return parent::processResetBulkAction($request, $filter);
    }
}
