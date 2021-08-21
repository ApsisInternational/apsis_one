<?php

namespace Apsis\One\Controller\Admin;

use Apsis\One\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShopBundle\Component\CsvResponse;
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
     * {@inheritdoc}
     */
    public function __construct(GridFactoryInterface $gridFactory, string $redirectRoute)
    {
        $this->redirectRoute = $redirectRoute;
        $this->gridFactory = $gridFactory;
        parent::__construct();
    }

    /**
     * @param Request $request
     * @param FilterInterface $filter
     *
     * @return Response
     */
    protected function processList(Request $request, FilterInterface $filter): Response
    {
        $grid = $this->gridFactory->getGrid($filter);
        return $this->render(
            self::TEMPLATES[$grid->getDefinition()->getId()],
            [$grid->getDefinition()->getId() => $this->presentGrid($grid)]
        );
    }

    /**
     * @param Request $request
     * @param FilterInterface $filter
     *
     * @return CsvResponse
     */
    protected function processExport(Request $request, FilterInterface $filter): CsvResponse
    {
        $grid = $this->gridFactory->getGrid(new $filter(['limit' => null] + $filter->all()));
        $headers = AbstractGridDefinitionFactory::getAllowedGridColumns($grid->getDefinition()->getId());
        $data = [];

        foreach ($grid->getData()->getRecords()->all() as $record) {
            $row = [];
            foreach ($headers as $header) {
                if (isset($record[$header])) {
                    $row[$header] = $record[$header];
                }
            }
            if (! empty($row)) {
                $data[] = $row;
            }
        }

        return (new CsvResponse())
            ->setData($data)
            ->setHeadersData($headers)
            ->setFileName($grid->getDefinition()->getId() . '_' . date('Y-m-d_His') . '.csv');
    }

    /**
     *
     * @AdminSecurity("is_granted(['delete'], request.get('_legacy_controller'))", message="Access denied.")
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
     * @AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function resetAction(Request $request): RedirectResponse
    {
        return $this->redirectToRoute($this->redirectRoute);
    }

    /**
     *
     * @AdminSecurity("is_granted(['delete'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteBulkAction(Request $request): RedirectResponse
    {
        return $this->redirectToRoute($this->redirectRoute);
    }

    /**
     *
     * @AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function resetBulkAction(Request $request): RedirectResponse
    {
        return $this->redirectToRoute($this->redirectRoute);
    }
}
