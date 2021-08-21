<?php

namespace Apsis\One\Grid\Definition\Factory;

use Apsis\One\Entity\EntityInterface as EI;
use Apsis\One\Form\ChoiceProvider\ProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\BooleanColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BadgeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractFilterableGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

abstract class AbstractGridDefinitionFactory extends AbstractFilterableGridDefinitionFactory
    implements GridDefinitionFactoryInterface
{
    /**
     * @var ProviderInterface|null
     */
    protected $provider;

    /**
     * {@inheritdoc}
     */
    public function __construct(HookDispatcherInterface $hookDispatcher, ?ProviderInterface $provider = null)
    {
        $this->provider = $provider;
        parent::__construct($hookDispatcher);
    }

    /**
     * {@inheritdoc}
     */
    protected function getId(): string
    {
        return static::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterId(): string
    {
        return $this->getId();
    }

    /**
     * {@inheritdoc}
     */
    protected function getName(): string
    {
        return EI::T_LABEL_MAPPINGS[$this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns(): ColumnCollectionInterface
    {
        $columnCollection = new ColumnCollection();
        $columns = array_merge(
            [self::COLUMN_TYPE_BULK_ACTION],
            static::getAllowedGridColumns($this->getId()),
            [self::COLUMN_TYPE_ACTIONS]
        );

        foreach ($columns as $column) {
            if ($column === EI::C_ID_SHOP) {
                $column = EI::PS_T_SHOP_C_NAME_ALIAS;
            }
            $columnCollection->add($this->createColumn($column));
        }

        return $columnCollection;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters(): FilterCollectionInterface
    {
        $filterCollection = new FilterCollection();
        $filters = array_merge(static::getAllowedGridFilters($this->getId()), ['actions' => SearchAndResetType::class]);

        if (isset($filters[EI::C_ID_SHOP])) {
            unset($filters[EI::C_ID_SHOP]);
        }

        foreach ($filters as $name => $type) {
            $filterCollection->add($this->createFilter($name, $type));
        }

        return $filterCollection;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add((new SubmitBulkAction('reset_selection'))
                ->setName('Reset selection')
                ->setOptions([
                    'submit_route' => self::GRID_ROUTES_RESET_MAP[$this->getId()],
                    'confirm_message' => 'Reset selected items sync status?',
                ])
            )
            ->add((new SubmitBulkAction('delete_selection'))
                ->setName('Delete selected')
                ->setOptions([
                    'submit_route' => self::GRID_ROUTES_DELETE_MAP[$this->getId()],
                    'confirm_message' => 'Delete selected items?'
                ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add(
                (new LinkGridAction('export'))
                    ->setName('Export')
                    ->setIcon('cloud_download')
                    ->setOptions(['route' => self::GRID_ROUTES_EXPORT_MAP[$this->getId()]])
            )
            ->add(
                (new SimpleGridAction('common_refresh_list'))
                    ->setName('Refresh list')
                    ->setIcon('refresh')
            )
            ->add(
                (new SimpleGridAction('common_show_query'))
                    ->setName('Show SQL query')
                    ->setIcon('code')
            );
    }

    /**
     * {@inheritdoc}
     */
    public static function getAllowedGridColumns(string $gridId): array
    {
        $columns = static::getAllowedColumns(EI::T_COLUMNS_MAPPINGS[$gridId]);
        return array_merge([EI::T_PRIMARY_MAPPINGS[$gridId]], array_keys($columns));
    }

    /**
     * {@inheritdoc}
     */
    public static function getAllowedGridFilters(string $gridId): array
    {
        $columns = static::getAllowedColumns(EI::T_COLUMNS_MAPPINGS[$gridId]);
        $columns = array_merge(
            [EI::T_PRIMARY_MAPPINGS[$gridId] => EI::C_PRIMARY_DEF[EI::T_PRIMARY_MAPPINGS[$gridId]]],
            $columns
        );

        $allowedFilters = [];
        foreach ($columns as $name => $definition) {
            $allowedFilters[$name] = self::FILTER_TYPE_MAPPINGS[$definition['validate']];
        }
        return $allowedFilters;
    }

    /**
     * @param string $column
     *
     * @return AbstractColumn
     */
    protected function createColumn(string $column): AbstractColumn
    {
        if ($column === self::COLUMN_TYPE_BULK_ACTION) {
            return (new BulkActionColumn('bulk_action'))
                ->setOptions([
                    'bulk_field' => EI::T_PRIMARY_MAPPINGS[$this->getId()],
                ]);
        }

        if ($column === self::COLUMN_TYPE_ACTIONS) {
            return (new ActionColumn($column))
                ->setName(ucfirst(self::COLUMN_TYPE_ACTIONS))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add(
                            (new LinkRowAction('reset'))
                                ->setName('Reset')
                                ->setIcon('edit')
                                ->setOptions([
                                    'route' => self::GRID_ROUTES_RESET_MAP[$this->getId()],
                                    'route_param_name' => EI::T_PRIMARY_MAPPINGS[$this->getId()],
                                    'route_param_field' => EI::T_PRIMARY_MAPPINGS[$this->getId()],
                                    'clickable_row' => false,
                                ])
                        )->add(
                            (new LinkRowAction('delete'))
                                ->setName('Delete')
                                ->setIcon('delete')
                                ->setOptions([
                                    'route' => self::GRID_ROUTES_DELETE_MAP[$this->getId()],
                                    'route_param_name' => EI::T_PRIMARY_MAPPINGS[$this->getId()],
                                    'route_param_field' => EI::T_PRIMARY_MAPPINGS[$this->getId()],
                                    'clickable_row' => false,
                                ])
                        )
                ]);
        }

        $name = $this->getLabel($column);
        $options = ['field' => $column];

        if (in_array($column, self::BOOLEAN_COLUMNS)) {
            $options['true_name'] = 'Yes';
            $options['false_name'] = 'No';
            $options['clickable'] = false;
            return (new BooleanColumn($column))
                ->setName($name)
                ->setOptions($options);
        }

        if ($column === EI::C_ERROR_MSG) {
            $options['badge_type'] = 'danger';
            $options['empty_value'] = '';
            $options['clickable'] = false;
            return (new BadgeColumn($column))
                ->setName($name)
                ->setOptions($options);
        }

        return (new DataColumn($column))
            ->setName($name)
            ->setOptions($options);
    }

    /**
     * @param string $name
     * @param string $type
     *
     * @return Filter
     */
    protected function createFilter(string $name, string $type): Filter
    {
        $filter = (new Filter($name, $type))
            ->setAssociatedColumn($name);

        if ($type === NumberType::class || $type === TextType::class) {
            $filter->setTypeOptions([
                'attr' => [
                    'placeholder' => $this->getLabel($name, true)
                ],
                'required' => false
            ]);
        }

        if ($type === DateRangeType::class) {
            $filter->setTypeOptions(['required' => false]);
        }

        if ($type === ChoiceType::class && $this->provider instanceof ProviderInterface) {
            $filter->setTypeOptions([
                'choices' => $this->provider->getChoices(),
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'choice_translation_domain' => false
            ]);
        }

        if ($type === SearchAndResetType::class) {
            $filter->setTypeOptions([
                'reset_route' => 'admin_common_reset_search_by_filter_id',
                'reset_route_params' => [
                    'filterId' => $this->getId(),
                ],
                'redirect_route' => self::GRID_ROUTES_LIST_MAP[$this->getId()]
            ]);
        }

        return $filter;
    }

    /**
     * @param string $column
     * @param bool $withPlaceHolder
     *
     * @return string
     */
    protected function getLabel(string $column, bool $withPlaceHolder = false): string
    {
        $label = EI::C_LABEL_MAPPINGS[$column];
        if ($withPlaceHolder) {
            $label = 'Search ' . $label;
        }
        return ucfirst($label);
    }

    /**
     * @param array $columns
     *
     * @return array
     */
    protected static function getAllowedColumns(array $columns): array
    {
        if (isset($columns[EI::C_EVENT_DATA])) {
            unset($columns[EI::C_EVENT_DATA]);
        }

        if (isset($columns[EI::C_SUB_EVENT_DATA])) {
            unset($columns[EI::C_SUB_EVENT_DATA]);
        }

        if (isset($columns[EI::C_CART_DATA])) {
            unset($columns[EI::C_CART_DATA]);
        }

        // Change position to end
        if (isset($columns[EI::C_ID_SHOP])) {
            $idShopColumn = $columns[EI::C_ID_SHOP];
            unset($columns[EI::C_ID_SHOP]);
            $columns[EI::C_ID_SHOP] = $idShopColumn;
        }

        return $columns;
    }
}