<?php

namespace Apsis\One\Grid\Query;

use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Apsis\One\Entity\EntityInterface as EI;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use Apsis\One\Grid\Definition\Factory\AbstractGridDefinitionFactory;

abstract class AbstractQueryBuilder extends AbstractDoctrineQueryBuilder implements QueryBuilderInterface
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $criteriaApplicator;

    /**
     * @var int
     */
    protected $contextShopId;

    /**
     * Get table name
     */
    abstract protected function getTableName(): string;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator,
        int $contextShopId
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->contextShopId = $contextShopId;
        $this->criteriaApplicator = $criteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $searchQueryBuilder = $this->getBaseQuery($searchCriteria)
            ->select($this->getGridColumnsWithTableAlias())
            ->addSelect($this->getColumn(EI::PS_T_SHOP_C_NAME, EI::PS_TABLE_SHOP_ALIAS));

        $this->criteriaApplicator->applySorting($searchCriteria, $searchQueryBuilder);
        $this->criteriaApplicator->applyPagination($searchCriteria, $searchQueryBuilder);

        return $searchQueryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQuery($searchCriteria)
            ->select('COUNT(*)');
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    protected function getBaseQuery(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $joinCondition = $this->formatString(
            $this->getColumn(EI::C_ID_SHOP),
            $this->getColumn(EI::C_ID_SHOP, EI::PS_TABLE_SHOP_ALIAS),
            ' = '
        );

        $queryBuilder = $this->connection->createQueryBuilder()
            ->from($this->getTable(), $this->getAlias())
            ->leftJoin(
                $this->getAlias(),
                $this->getTable(EI::PS_TABLE_SHOP),
                EI::PS_TABLE_SHOP_ALIAS,
                $joinCondition
            )
            ->where($this->formatString($this->getColumn(EI::C_ID_SHOP), EI::PS_SHOP_ID_PARAM, ' = :'))
            ->setParameter(EI::PS_SHOP_ID_PARAM, $this->contextShopId);

        $this->applyFilters(
            $searchCriteria->getFilters(),
            $queryBuilder,
            array_keys(AbstractGridDefinitionFactory::getAllowedGridFilters(static::getTableName()))
        );

        return $queryBuilder;
    }

    /**
     * @param array $filters
     * @param QueryBuilder $queryBuilder
     * @param array $allowedFilters
     */
    protected function applyFilters(array $filters, QueryBuilder $queryBuilder, array $allowedFilters): void
    {
        if (isset($allowedFilters[EI::C_ID_SHOP])) {
            unset($allowedFilters[EI::C_ID_SHOP]);
        }

        foreach ($filters as $filterName => $filterValue) {
            if (! in_array($filterName, $allowedFilters)) {
                continue;
            }

            if (in_array($filterName, $this->getIntTypesFilters($allowedFilters))) {
                $queryBuilder
                    ->andWhere($this->formatString($this->getColumn('`' . $filterName) . '`', $filterName, ' = :'))
                    ->setParameter($filterName, $filterValue);

                continue;
            }

            if (in_array($filterName, $this->getDateRangeTypeFilters($allowedFilters))) {
                $cName = $this->getColumn($filterName);
                $left = $this->formatString($cName, 'date_from', ' >= :');
                $right = $this->formatString($cName, 'date_to', ' <= :');
                $to = $this->formatString($filterValue['to'], '23:59:59', ' ');
                $from = $this->formatString($filterValue['from'], '0:0:0', ' ');

                $queryBuilder->andWhere($this->formatString($left, $right, ' AND '))
                    ->setParameter('date_from', $from)
                    ->setParameter('date_to', $to);

                if (isset($filterValue['from'])) {
                    $queryBuilder->andWhere($left)
                        ->setParameter('date_from', $from);
                }

                if (isset($filterValue['to'])) {
                    $queryBuilder->andWhere($right)
                        ->setParameter('date_to', $to);
                }

                continue;
            }

            $queryBuilder->andWhere($this->formatString('`' . $filterName . '`', $filterName, ' LIKE :'))
                ->setParameter($filterName, $this->formatString('%', '%', $filterValue));
        }
    }

    /**
     * @return array
     */
    protected function getGridColumnsWithTableAlias(): array
    {
        $columns = [];
        foreach (AbstractGridDefinitionFactory::getAllowedGridColumns(static::getTableName()) as $allowedGridColumn) {
            $columns[] = $this->getColumn($allowedGridColumn);
        }
        return $columns;
    }

    /**
     * @param array $allowedFilters
     *
     * @return array
     */
    protected function getIntTypesFilters(array $allowedFilters): array
    {
        $intTypeFilters = [];
        foreach ($allowedFilters as $column => $type) {
            if (in_array($type, self::INT_FILTER_TYPES)) {
                $intTypeFilters[] = $column;
            }
        }
        return $intTypeFilters;
    }

    /**
     * @param array $allowedFilters
     *
     * @return array
     */
    protected function getDateRangeTypeFilters(array $allowedFilters): array
    {
        $dateRangeTypeFilters = [];
        foreach ($allowedFilters as $column => $type) {
            if ($type === DateRangeType::class) {
                $dateRangeTypeFilters[] = $column;
            }
        }
        return $dateRangeTypeFilters;
    }

    /**
     * @return string
     */
    protected function getAlias(): string
    {
        return EI::T_ALIAS_MAPPINGS[static::getTableName()];
    }

    /**
     * @param string|null $table
     *
     * @return string
     */
    protected function getTable(?string $table = null): string
    {
        if (strlen((string) $table)) {
            return $this->dbPrefix . $table;
        }

        return $this->dbPrefix . static::getTableName();
    }

    /**
     * @param string $column
     * @param string|null $alias
     *
     * @return string
     */
    protected function getColumn(string $column, ?string $alias = null): string
    {
        if (strlen((string) $alias)) {
            $cName = $this->formatString($alias, $column, '.');

            if ($column === EI::PS_T_SHOP_C_NAME) {
                return $this->formatString($cName, EI::PS_T_SHOP_C_NAME_ALIAS, ' as ');
            }

            return $cName;
        }

        return $this->formatString($this->getAlias(), $column, '.');
    }

    /**
     * @param string $left
     * @param string $right
     * @param string $middle
     *
     * @return string
     */
    protected function formatString(string $left, string $right, string $middle): string
    {
        return sprintf('%s%s%s', $left, $middle, $right);
    }
}