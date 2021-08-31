<?php

namespace Apsis\One\Grid\Data\Factory;

use Apsis\One\Entity\EntityInterface as EI;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Apsis\One\Form\ChoiceProvider\ProviderInterface;

abstract class AbstractGridDataFactoryDecorator implements GridDataFactoryInterface
{
    const NO_ID = 0;
    const NO_ID_COLUMNS = [EI::C_ID_CUSTOMER, EI::C_ID_NEWSLETTER];

    /**
     * @var GridDataFactoryInterface
     */
    protected $apsisGridDataFactory;

    /**
     * @var ProviderInterface
     */
    protected $syncStatusProvider;

    /**
     * @var ProviderInterface|null
     */
    protected $eventTypeProvider;

    /**
     * @return array
     */
    abstract protected function getColumns(): array;

    /**
     * AbstractGridDataFactoryDecorator constructor.
     *
     * @param GridDataFactoryInterface $apsisGridDataFactory
     * @param ProviderInterface $syncStatusProvider
     * @param ProviderInterface|null $eventTypeProvider
     */
    public function __construct(
        GridDataFactoryInterface $apsisGridDataFactory,
        ProviderInterface $syncStatusProvider,
        ?ProviderInterface $eventTypeProvider = null
    ) {
        $this->apsisGridDataFactory = $apsisGridDataFactory;
        $this->syncStatusProvider = $syncStatusProvider;
        $this->eventTypeProvider = $eventTypeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria): GridData
    {
        $apsisData = $this->apsisGridDataFactory->getData($searchCriteria);
        $records = $this->modifyColumnsValue($apsisData->getRecords(), static::getColumns());

        return new GridData(
            $records,
            $apsisData->getRecordsTotal(),
            $apsisData->getQuery()
        );
    }

    /**
     * @param RecordCollectionInterface $collection
     * @param array $columns
     *
     * @return RecordCollection
     */
    protected function modifyColumnsValue(RecordCollectionInterface $collection, array $columns): RecordCollection
    {
        $records = $collection->all();
        foreach ($columns as $column => $cond) {
            foreach ($records as $index => $record) {
                $records[$index] = $this->applyModifications($record, $column, is_array($cond) ? $cond : [$cond]);
            }
        }
        return new RecordCollection($records);
    }

    /**
     * @param array $record
     * @param string $column
     * @param array $cond
     *
     * @return array
     */
    protected function applyModifications(array $record, string $column, array $cond): array
    {
        if (isset($record[$column])) {
            if ($column === EI::C_SYNC_STATUS && array_key_exists($record[$column], $cond)) {
                $record[$column] = $cond[$record[$column]];
            }

            if (in_array($column, self::NO_ID_COLUMNS) && $record[$column] == array_pop($cond)) {
                $record[$column] = '--';
            }
        }

        return $record;
    }
}
