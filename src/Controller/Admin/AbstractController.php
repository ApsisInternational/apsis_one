<?php

namespace Apsis\One\Controller\Admin;

use Apsis\One\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Apsis\One\Grid\Search\Filters\FilterInterface;
use Apsis\One\Model\EntityInterface as EI;
use Apsis\One\Grid\Definition\Factory\GridDefinitionFactoryInterface as GDFI;
use Throwable;

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
        try {
            $grid = $this->gridFactory->getGrid($filter);
            return $this->render(
                self::TEMPLATES[$grid->getDefinition()->getId()],
                [$grid->getDefinition()->getId() => $this->presentGrid($grid)]
            );
        } catch (Throwable $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute($this->redirectRoute);
        }
    }

    /**
     * @param Request $request
     * @param FilterInterface $filter
     *
     * @return Response
     */
    protected function processExport(Request $request, FilterInterface $filter): Response
    {
        try {
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
        } catch (Throwable $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute($this->redirectRoute);
        }
    }

    /**
     * @param Request $request
     * @param FilterInterface $filter
     *
     * @return Response
     */
    public function processReset(Request $request, FilterInterface $filter): Response
    {
        try {
            $this->resetSelection($this->getArrForResetDelete($request, $filter));
        } catch (Throwable $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute($this->redirectRoute);
    }

    /**
     * @param Request $request
     * @param FilterInterface $filter
     *
     * @return Response
     */
    public function processResetBulkAction(Request $request, FilterInterface $filter): Response
    {
        try {
            $this->resetSelection($this->getArrForResetDelete($request, $filter, true));
        } catch (Throwable $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute($this->redirectRoute);
    }

    /**
     * @param Request $request
     * @param FilterInterface $filter
     *
     * @return Response
     */
    public function processDelete(Request $request, FilterInterface $filter): Response
    {
        try {
            $this->deleteSelection($this->getArrForResetDelete($request, $filter));
        } catch (Throwable $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute($this->redirectRoute);
    }

    /**
     * @param Request $request
     * @param FilterInterface $filter
     *
     * @return Response
     */
    public function processDeleteBulk(Request $request, FilterInterface $filter): Response
    {
        try {
            $this->deleteSelection($this->getArrForResetDelete($request, $filter, true));
        } catch (Throwable $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute($this->redirectRoute);
    }

    /**
     * @param Request $request
     * @param FilterInterface $filter
     * @param bool $isBulk
     *
     * @return array
     */
    protected function getArrForResetDelete(Request $request, FilterInterface $filter, bool $isBulk = false): array
    {
        try {
            $grid = $this->gridFactory->getGrid($filter);

            if ($isBulk) {
                $ids = $request->get($grid->getDefinition()->getId() . '_bulk_action');
            } else {
                $ids = [$request->get(EI::T_PRIMARY_MAPPINGS[$grid->getDefinition()->getId()])];
            }

            return [
                'ids' => $ids,
                'class' => GDFI::GRID_ENTITY_CLASSNAME_MAP[$grid->getDefinition()->getId()]
            ];
        } catch (Throwable $e) {
            $this->addFlash('error', $e->getMessage());
            return [];
        }
    }

    /**
     * @param array $arr
     */
    protected function deleteSelection(array $arr): void
    {
        $status = false;

        try {
            if (! empty($arr['class']) && ! empty($arr['ids'])) {
                $status = (new $arr['class'])->deleteSelection($arr['ids']);
            }
        } catch (Throwable $e) {
            $this->addFlash('error', $e->getMessage());
            return;
        }

        $this->addJobStatusFlashMessage($status);
    }

    /**
     * @param array $arr
     */
    protected function resetSelection(array $arr): void
    {
        $status = false;

        try {
            if (! empty($arr['class']) && ! empty($arr['ids'])) {
                $status = (new $arr['class'])->resetSelection($arr['ids'], $arr['class']);
            }
        } catch (Throwable $e) {
            $this->addFlash('error', $e->getMessage());
            return;
        }

        $this->addJobStatusFlashMessage($status);
    }

    /**
     * @param bool $status
     */
    protected function addJobStatusFlashMessage(bool $status): void
    {
        if ($status) {
            $this->addFlash('success', 'Successfully completed action.');
        } else {
            $this->addFlash('error', 'Unable to complete action.');
        }
    }
}
