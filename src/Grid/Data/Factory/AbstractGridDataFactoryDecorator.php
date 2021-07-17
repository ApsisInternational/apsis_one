<?php

namespace Apsis\One\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Apsis\One\Form\ChoiceProvider\ProviderInterface;

abstract class AbstractGridDataFactoryDecorator implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    protected $apsisGridDataFactory;

    /**
     * @var ProviderInterface
     */
    protected $choiceProvider;

    /**
     * @return array
     */
    abstract protected function getColumns(): array;

    /**
     * AbstractGridDataFactoryDecorator constructor.
     *
     * @param GridDataFactoryInterface $apsisGridDataFactory
     * @param ProviderInterface $provider
     */
    public function __construct(GridDataFactoryInterface $apsisGridDataFactory, ProviderInterface $provider)
    {
        $this->apsisGridDataFactory = $apsisGridDataFactory;
        $this->choiceProvider = $provider;
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
     * @param RecordCollectionInterface $records
     * @param array $columns
     *
     * @return RecordCollection
     */
    protected function modifyColumnsValue(RecordCollectionInterface $records, array $columns): RecordCollection
    {
        $modifiedRecords = [];
        foreach ($columns as $column => $choiceValuePair) {
            foreach ($records as $record) {
                if (isset($record[$column]) && array_key_exists($record[$column], $choiceValuePair)) {
                    $record[$column] = $choiceValuePair[$record[$column]];
                }
                $modifiedRecords[] = $record;
            }
        }
        return new RecordCollection($modifiedRecords);
    }
}
