<?php

namespace Apsis\One\Controller\Admin;

use Apsis\One\Grid\Search\Filters\ProfileFilters;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PrestaShopBundle\Security\Annotation\AdminSecurity;

class ProfileController extends AbstractController
{
    /**
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param ProfileFilters $filter
     *
     * @return Response
     */
    public function indexAction(Request $request, ProfileFilters $filter): Response
    {
        return $this->processList($request, $filter);
    }

    /**
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param ProfileFilters $filter
     *
     * @return Response
     */
    public function exportAction(Request $request, ProfileFilters $filter): Response
    {
        return $this->processExport($request, $filter);
    }

    /**
     *
     * @AdminSecurity("is_granted(['delete'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param ProfileFilters $filter
     *
     * @return Response
     */
    public function deleteAction(Request $request, ProfileFilters $filter): Response
    {
        return parent::processDelete($request, $filter);
    }

    /**
     *
     * @AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param ProfileFilters $filter
     *
     * @return Response
     */
    public function resetAction(Request $request, ProfileFilters $filter): Response
    {
        return parent::processReset($request, $filter);
    }

    /**
     *
     * @AdminSecurity("is_granted(['delete'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param ProfileFilters $filter
     *
     * @return Response
     */
    public function deleteBulkAction(Request $request, ProfileFilters $filter): Response
    {
        return parent::processDeleteBulk($request, $filter);
    }

    /**
     *
     * @AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param ProfileFilters $filter
     *
     * @return Response
     */
    public function resetBulkAction(Request $request, ProfileFilters $filter): Response
    {
        return parent::processResetBulkAction($request, $filter);
    }
}
