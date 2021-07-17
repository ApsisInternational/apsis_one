<?php

namespace Apsis\One\Controller\Admin;

use PrestaShop\PrestaShop\Core\Grid\Filter\GridFilterFormFactoryInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Apsis\One\Grid\Search\Filters\FilterInterface;
use PrestaShopBundle\Security\Annotation\AdminSecurity;

abstract class AbstractController extends FrameworkBundleAdminController implements ControllerInterface
{
    /**
     * @var GridFactoryInterface
     */
    protected $gridFactory;

    /**
     * @var string
     */
    protected $redirectRoute;

    /**
     * 'prestashop.core.grid.filter.form_factory'
     *
     * @var GridFilterFormFactoryInterface
     */
    protected $filterFormFactory;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        GridFactoryInterface $gridFactory,
        GridFilterFormFactoryInterface $filterFormFactory,
        string $redirectRoute
    ) {
        $this->redirectRoute = $redirectRoute;
        $this->gridFactory = $gridFactory;
        $this->filterFormFactory = $filterFormFactory;
        parent::__construct();
    }

    /**
     * @param Request $request
     * @param FilterInterface $filter
     *
     * @return Response
     */
    public function processList(Request $request, FilterInterface $filter): Response
    {
        $grid = $this->gridFactory->getGrid($filter);
        return $this->render(
            self::TEMPLATES[$grid->getDefinition()->getId()],
            [$grid->getDefinition()->getId() => $this->presentGrid($grid)]
        );
    }

    /**
     * @AdminSecurity("is_granted(['read', 'create', 'update', 'delete'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param FilterInterface $filter
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request, FilterInterface $filter): RedirectResponse
    {
        $filtersForm = $this->filterFormFactory->create($this->gridFactory->getGrid($filter)->getDefinition());
        $filtersForm->handleRequest($request);

        $filters = [];
        if ($filtersForm->isSubmitted()) {
            $filters = $filtersForm->getData();
        }
        return $this->redirectToRoute($this->redirectRoute, ['filters' => $filters]);
    }

    /**
     *
     * @AdminSecurity("is_granted(['read', 'create', 'update', 'delete'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request): RedirectResponse
    {
        return $this->redirectToRoute($this->redirectRoute);
    }

    /**
     *
     * @AdminSecurity("is_granted(['read', 'create', 'update', 'delete'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function resetAction(Request $request): RedirectResponse
    {
        return $this->redirectToRoute($this->redirectRoute);
    }
}