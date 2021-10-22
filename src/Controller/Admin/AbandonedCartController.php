<?php

namespace Apsis\One\Controller\Admin;

use Apsis\One\Grid\Search\Filters\AbandonedCartFilters;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PrestaShopBundle\Security\Annotation\AdminSecurity;

class AbandonedCartController extends AbstractController
{
    /**
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param AbandonedCartFilters $filter
     *
     * @return Response
     */
    public function indexAction(Request $request, AbandonedCartFilters $filter): Response
    {
        return $this->processList($request, $filter);
    }

    /**
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param AbandonedCartFilters $filter
     *
     * @return Response
     */
    public function exportAction(Request $request, AbandonedCartFilters $filter): Response
    {
        return $this->processExport($request, $filter);
    }

    /**
     *
     * @AdminSecurity("is_granted(['delete'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param AbandonedCartFilters $filter
     *
     * @return Response
     */
    public function deleteAction(Request $request, AbandonedCartFilters $filter): Response
    {
        return parent::processDelete($request, $filter);
    }

    /**
     *
     * @AdminSecurity("is_granted(['delete'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param AbandonedCartFilters $filter
     *
     * @return Response
     */
    public function deleteBulkAction(Request $request, AbandonedCartFilters $filter): Response
    {
        return parent::processDeleteBulk($request, $filter);
    }
}
