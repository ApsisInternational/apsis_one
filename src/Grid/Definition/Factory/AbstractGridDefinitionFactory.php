<?php

namespace Apsis\One\Grid\Definition\Factory;

use Apsis\One\Model\EntityInterface as EI;
use Apsis\One\Form\ChoiceProvider\ProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\BooleanColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BadgeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\IdentifierColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\LinkColumn;
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
    protected $syncStatusProvider;

    /**
     * @var ProviderInterface|null
     */
    protected $eventTypeProvider;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        ?ProviderInterface $syncStatusProvider = null,
        ?ProviderInterface $eventTypeProvider = null
    ) {
        $this->syncStatusProvider = $syncStatusProvider;
        $this->eventTypeProvider = $eventTypeProvider;
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
        $filters = array_merge(
            static::getAllowedGridFilters($this->getId()),
            [self::COLUMN_TYPE_ACTIONS => SearchAndResetType::class]
        );

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
        $bulkActions =  (new BulkActionCollection())
            ->add((new SubmitBulkAction('delete_selection'))
                ->setName('Delete Selected')
                ->setOptions([
                    'submit_route' => self::GRID_ROUTES_DELETE_BULK_MAP[$this->getId()],
                    'confirm_message' => 'Delete selected records?'
                ])
            );

        if ($this->getId() !== AbandonedCartGridDefinitionFactory::GRID_ID) {
            $bulkActions->add((new SubmitBulkAction('reset_selection'))
                ->setName('Reset Selected')
                ->setOptions([
                    'submit_route' => self::GRID_ROUTES_RESET_BULK_MAP[$this->getId()],
                    'confirm_message' => 'Reset sync status for selected records?',
                ])
            );
        }

        return $bulkActions;
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
            )
            ->add(
                (new SimpleGridAction('common_export_sql_manager'))
                    ->setName('Export to SQL Manager')
                    ->setIcon('storage')
            );
    }

    /**
     * @return RowActionCollectionInterface
     */
    protected function getRowActions(): RowActionCollectionInterface
    {
        $rowActions = (new RowActionCollection())
            ->add(
                (new SubmitRowAction('delete'))
                    ->setName('Delete')
                    ->setIcon('delete')
                    ->setOptions([
                        'route' => self::GRID_ROUTES_DELETE_MAP[$this->getId()],
                        'route_param_name' => EI::T_PRIMARY_MAPPINGS[$this->getId()],
                        'route_param_field' => EI::T_PRIMARY_MAPPINGS[$this->getId()],
                        'confirm_message' => 'Delete this record?'
                    ])
            );

        if ($this->getId() !== AbandonedCartGridDefinitionFactory::GRID_ID) {
            $rowActions->add(
                (new SubmitRowAction('reset'))
                    ->setName('Reset')
                    ->setIcon('edit')
                    ->setOptions([
                        'route' => self::GRID_ROUTES_RESET_MAP[$this->getId()],
                        'route_param_name' => EI::T_PRIMARY_MAPPINGS[$this->getId()],
                        'route_param_field' => EI::T_PRIMARY_MAPPINGS[$this->getId()],
                        'confirm_message' => 'Reset sync status for this record?'
                    ])
            );
        }

        if ($this->getId() === ProfileGridDefinitionFactory::GRID_ID) {
            $rowActions->add(
                (new LinkRowAction('events'))
                    ->setName('Linked Events')
                    ->setIcon('zoom_in')
                    ->setOptions([
                        'route' => self::GRID_ROUTES_LIST_MAP[EventGridDefinitionFactory::GRID_ID],
                        'route_param_name' => sprintf(
                            "%s[filters][%s]",
                            EventGridDefinitionFactory::GRID_ID,
                            EI::T_PRIMARY_MAPPINGS[$this->getId()]
                        ),
                        'route_param_field' => EI::T_PRIMARY_MAPPINGS[$this->getId()]
                    ])
            )->add(
                (new LinkRowAction('abandoned_carts'))
                    ->setName('Linked Abandoned Carts')
                    ->setIcon('zoom_in')
                    ->setOptions([
                        'route' => self::GRID_ROUTES_LIST_MAP[AbandonedCartGridDefinitionFactory::GRID_ID],
                        'route_param_name' => sprintf(
                            "%s[filters][%s]",
                            AbandonedCartGridDefinitionFactory::GRID_ID,
                            EI::T_PRIMARY_MAPPINGS[$this->getId()]
                        ),
                        'route_param_field' => EI::T_PRIMARY_MAPPINGS[$this->getId()]
                    ])
            );
        }

        return $rowActions;
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
        $allowedFilters = [];
        foreach (static::getAllowedColumns(EI::T_COLUMNS_MAPPINGS[$gridId]) as $name => $definition) {
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
            return (new BulkActionColumn(self::COLUMN_TYPE_BULK_ACTION))
                ->setOptions([
                    'bulk_field' => EI::T_PRIMARY_MAPPINGS[$this->getId()],
                ]);
        }

        if ($column === self::COLUMN_TYPE_ACTIONS) {
            return (new ActionColumn($column))
                ->setName(ucfirst(self::COLUMN_TYPE_ACTIONS))
                ->setOptions(['actions' => $this->getRowActions()]);
        }

        if ($column === EI::T_PRIMARY_MAPPINGS[$this->getId()]) {
            return (new IdentifierColumn($column))
                ->setName('ID')
                ->setOptions(['identifier_field' => $column]);
        }

        if ($column === EI::C_ID_PROFILE && $column !== EI::T_PRIMARY_MAPPINGS[$this->getId()]) {
            return (new LinkColumn($column))
                ->setName($this->getLabel($column))
                ->setOptions([
                    'field' => $column,
                    'route' => self::GRID_ROUTES_LIST_MAP[ProfileGridDefinitionFactory::GRID_ID],
                    'route_param_name' => sprintf("%s[filters][%s]", ProfileGridDefinitionFactory::GRID_ID, $column),
                    'route_param_field' => $column,
                    'icon' => '',
                    'button_template' => 'outline',
                ]);
        }

        $options = ['field' => $column];

        if (in_array($column, self::BOOLEAN_COLUMNS)) {
            $options['true_name'] = 'Yes';
            $options['false_name'] = 'No';
            $options['clickable'] = false;
            return (new BooleanColumn($column))
                ->setName($this->getLabel($column) . '?')
                ->setOptions($options);
        }

        if ($column === EI::C_ERROR_MSG) {
            $options['badge_type'] = 'danger';
            $options['empty_value'] = '';
            $options['clickable'] = false;
            return (new BadgeColumn($column))
                ->setName($this->getLabel($column))
                ->setOptions($options);
        }

        return (new DataColumn($column))
            ->setName($this->getLabel($column))
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

        if ($type === ChoiceType::class) {
            $choices = [];

            if ($name === EI::C_SYNC_STATUS  && $this->syncStatusProvider instanceof ProviderInterface) {
                $choices = $this->syncStatusProvider->getChoices();
            }

            if ($name === EI::C_EVENT_TYPE  && $this->eventTypeProvider instanceof ProviderInterface) {
                $choices = $this->eventTypeProvider->getChoices();
            }

            $filter->setTypeOptions([
                'choices' => $choices,
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
        foreach (self::NOT_ALLOWED_COLUMNS as $notAllowedColumn) {
            if (isset($columns[$notAllowedColumn])) {
                unset($columns[$notAllowedColumn]);
            }
        }
        return $columns;
    }
}
