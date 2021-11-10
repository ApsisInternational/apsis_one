<?php

namespace Apsis\One\Model;

use Apsis\One\Helper\DateHelper;
use Apsis\One\Helper\EntityHelper;
use Apsis\One\Helper\HelperInterface;
use Apsis\One\Helper\ModuleHelper;
use libphonenumber\PhoneNumberUtil;
use Exception;
use Throwable;

abstract class AbstractData implements DataInterface
{
    /**
     * @var ModuleHelper
     */
    protected $helper;

    /**
     * @var array
     */
    protected $objectData;

    /**
     * @var array
     */
    protected $dataArr = [];

    /**
     * @var array
     */
    protected $addressArr = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(HelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function setObjectData(array $objectDataArr, SchemaInterface $schema): DataInterface
    {
        $this->dataArr = $this->addressArr = [];
        $this->objectData = $objectDataArr;
        $definition = $schema->getDefinition();
        foreach ($schema->getDefinitionTypes() as $schemaType) {
            $this->dataArr[$schemaType] = $this->getDataByDefinitionType($schemaType, $definition[$schemaType]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataArr(): array
    {
        return $this->dataArr;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return (string) json_encode($this->getDataArr());
    }

    /**
     * @param string $logicalName
     * @param string $type
     * @param string $validate
     *
     * @return mixed
     *
     * @throws Throwable
     */
    protected function getValue(string $logicalName, string $type, string $validate)
    {
        try {
            $value = call_user_func(['self', 'get' . ucfirst($logicalName)], $type);
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
            $value = null;
        }

        return $this->validate($value, $validate);
    }

    /**
     * @param mixed $value
     * @param string $validate
     *
     * @return mixed
     *
     * @throws Throwable
     */
    private function validate($value, string $validate)
    {
        if (in_array($validate, SchemaInterface::NOT_NULL_VALIDATIONS) && empty($value)) {
            $msg = __METHOD__ . " - Invalid value (" . var_export($value, true) . ") for validation ($validate)";
            throw new Exception($msg);
        }

        return $value;
    }

    /**
     * @param string $definitionType
     * @param array $definition
     *
     * @return array|string
     *
     * @throws Throwable
     */
    private function getDataByDefinitionType(string $definitionType, array $definition)
    {
        if ($definitionType === SchemaInterface::PROFILE_SCHEMA_TYPE_ENTRY) {
            return $this->getDataByDefinitionTypeEntry($definition);
        } elseif ($definitionType === SchemaInterface::KEY_ITEMS) {
            return $this->getDataByDefinitionTypeItems($definition);
        } elseif ($definitionType === SchemaInterface::PROFILE_SCHEMA_TYPE_EVENTS) {
            return $this->getDataByDefinitionTypeEvents();
        }

        $data = [];
        foreach ($definition as $schemaItem) {
            try {
                $logicalName = $schemaItem[SchemaInterface::SCHEMA_KEY_LOGICAL_NAME];
                $data[$logicalName] = $this->getValue(
                    $logicalName,
                    $schemaItem[SchemaInterface::SCHEMA_KEY_TYPE],
                    $schemaItem[SchemaInterface::SCHEMA_KEY_VALIDATE]
                );
            } catch (Throwable $e) {
                $this->helper->logErrorMsg(__METHOD__, $e);
            }
        }

        return $data;
    }

    /**
     * @param string $key
     * @param bool $isPhone
     * @param int $addressType
     * @param bool $secondTry
     */
    private function setAddressArr(string $key, bool $isPhone, int $addressType, bool $secondTry = false): void
    {
        try {
            $addArr = $this->objectData[self::KEY_ADD_COL];
            krsort($addArr);

            if (! empty($this->objectData[self::KEY_ADD_IDS][self::ADD_TYPE_MAP[$addressType]]) &&
                isset($addArr[$this->objectData[self::KEY_ADD_IDS][self::ADD_TYPE_MAP[$addressType]]])
            ) {
                $this->addressArr[$addressType] =
                    $addArr[$this->objectData[self::KEY_ADD_IDS][self::ADD_TYPE_MAP[$addressType]]];
            } elseif (! $secondTry) {
                $this->setAddressArr($key, $isPhone, $this->getOtherAddressType($addressType), true);
            } elseif (is_array($addressArr = reset($addArr))) {
                $otherAddress = $this->getOtherAddressType($addressType);
                $this->addressArr[$addressType] = $this->addressArr[$otherAddress] = $addressArr;
            }
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param string $key
     * @param string $type
     * @param bool $isPhone
     * @param int $addressType
     *
     * @return bool|float|int|string|null
     */
    protected function getFormattedValueByType(string $key, string $type, bool $isPhone = false, int $addressType = 0)
    {
        $value = null;

        try {
            if (isset($this->objectData[$key])) {
                $value = $this->objectData[$key];
            } elseif (isset($this->objectData[self::KEY_SALES][$key])) {
                $value = $this->objectData[self::KEY_SALES][$key];
            } elseif (isset(self::ADD_TYPE_MAP[$addressType]) && isset($this->addressArr[$addressType])) {
                $value = $this->addressArr[$addressType][$key] ?? null;
            } elseif (isset(self::ADD_TYPE_MAP[$addressType]) && ! empty($this->objectData[self::KEY_ADD_COL])) {
                $this->setAddressArr($key, $isPhone, $addressType);
                $value = $this->addressArr[$addressType][$key] ?? null;
            }

            if ($isPhone && ! empty($value) && ! empty($this->addressArr[$addressType]['country_code'])) {
                $value = $this->validateAndFormatMobileNumber($this->addressArr[$addressType]['country_code'], $value);
            }

            switch ($type) {
                case SchemaInterface::DATA_TYPE_DOUBLE:
                case SchemaInterface::DATA_TYPE_INT:
                    $type = ! empty($value) && is_numeric($value) ? $type : 'null';
                    break;
                case SchemaInterface::DATA_TYPE_STRING:
                    $type = ! empty($value) && is_string($value) ? $type : 'null';
                    break;
                case SchemaInterface::DATA_TYPE_BOOLEAN:
                    $value = isset($value) && (is_bool($value) || in_array($value, [0, 1])) ? $value : 'null';
                    break;
                default:
                    $type = 'null';
            }

            settype($value, $type);
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
        }

        return $value;
    }

    /**
     * @param int $addressType
     *
     * @return int
     */
    private function getOtherAddressType(int $addressType): int
    {
        if ($addressType === self::ADD_TYPE_BILLING) {
            $getFromOtherAddressType = self::ADD_TYPE_SHIPPING;
        } else {
            $getFromOtherAddressType = self::ADD_TYPE_BILLING;
        }

        return $getFromOtherAddressType;
    }

    /**
     * @param string $countryCode
     * @param string $phoneNumber
     *
     * @return int|null
     */
    private function validateAndFormatMobileNumber(string $countryCode, string $phoneNumber): ?int
    {
        $formattedNumber = null;

        try {
            if (strlen($countryCode) === 2) {
                $phoneUtil = PhoneNumberUtil::getInstance();
                $numberProto = $phoneUtil->parse($phoneNumber, $countryCode);
                if ($phoneUtil->isValidNumber($numberProto)) {
                    $formattedNumber = (int) sprintf(
                        "%d%d",
                        (int) $numberProto->getCountryCode(),
                        (int) $numberProto->getNationalNumber()
                    );
                }
            }
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
        }

        return $formattedNumber;
    }

    /**
     * @param array $definition
     *
     * @return array
     *
     * @throws Throwable
     */
    private function getDataByDefinitionTypeItems(array $definition): array
    {
        $itemsDataArr = [];
        if (empty($this->objectData[SchemaInterface::KEY_ITEMS])) {
            return $itemsDataArr;
        }

        /** @var SchemaInterface $itemSchema */
        $itemSchema = $this->helper->getService($definition[SchemaInterface::KEY_SCHEMA]);
        /** @var DataInterface $itemContainer */
        $itemContainer = $this->helper->getService($definition[SchemaInterface::KEY_CONTAINER]);
        foreach ($this->objectData[SchemaInterface::KEY_ITEMS] as $item) {
            $itemsDataArr[] = $itemContainer->setObjectData($item, $itemSchema)->getDataArr();
        }
        return $itemsDataArr;
    }

    /**
     * @param array $definition
     *
     * @return string|null
     *
     * @throws Throwable
     */
    private function getDataByDefinitionTypeEntry(array $definition): ?string
    {
        return (string) $this->getValue(
            SchemaInterface::SCHEMA_ENTRY_ID_FIELD_NAME,
            $definition[SchemaInterface::SCHEMA_KEY_TYPE],
            $definition[SchemaInterface::SCHEMA_KEY_VALIDATE]
        );
    }

    /**
     * @return array
     */
    private function getDataByDefinitionTypeEvents(): array
    {
        $eventsArr = [];
        try {
            if (empty($this->objectData[SchemaInterface::PROFILE_SCHEMA_TYPE_EVENTS])) {
                return $eventsArr;
            }

            /** @var EntityHelper $entityHelper */
            $entityHelper = $this->helper->getService(HelperInterface::SERVICE_HELPER_ENTITY);
            /** @var DateHelper $dateHelper */
            $dateHelper = $this->helper->getService(HelperInterface::SERVICE_HELPER_DATE);

            /** @var Event $event */
            foreach ($this->objectData[SchemaInterface::PROFILE_SCHEMA_TYPE_EVENTS] as $event) {
                try {
                    $eventDataArr = $entityHelper->getEventDataArrForExport(
                        json_decode($event->getEventData(), true),
                        $event->getEventType()
                    );

                    if (empty($eventDataArr[SchemaInterface::KEY_MAIN])) {
                        continue;
                    }

                    if (isset($eventDataArr[SchemaInterface::KEY_ITEMS])) {
                        $subEvents = $eventDataArr[SchemaInterface::KEY_ITEMS];
                        unset($eventDataArr[SchemaInterface::KEY_ITEMS]);
                    }

                    $discriminator = SchemaInterface::EVENT_TYPE_TO_DISCRIMINATOR_MAP[$event->getEventType()];
                    $eventsArr[] = [
                        SchemaInterface::SCHEMA_PROFILE_EVENT_ITEM_TIME =>
                            (int) $dateHelper->formatDateForPlatformCompatibility($event->getDateAdd()),
                        SchemaInterface::SCHEMA_PROFILE_EVENT_ITEM_DISCRIMINATOR =>
                            is_array($discriminator) ? $discriminator[SchemaInterface::KEY_MAIN] : $discriminator,
                        SchemaInterface::SCHEMA_PROFILE_EVENT_ITEM_DATA => $eventDataArr[SchemaInterface::KEY_MAIN]
                    ];

                    if (is_array($discriminator) && ! empty($subEvents)) {
                        foreach ($subEvents as $subEvent) {
                            if (empty($subEvent[SchemaInterface::KEY_MAIN])) {
                                continue;
                            }

                            $eventsArr[] = [
                                SchemaInterface::SCHEMA_PROFILE_EVENT_ITEM_TIME =>
                                    (int) $dateHelper->formatDateForPlatformCompatibility($event->getDateAdd()),
                                SchemaInterface::SCHEMA_PROFILE_EVENT_ITEM_DISCRIMINATOR =>
                                    $discriminator[SchemaInterface::KEY_ITEMS],
                                SchemaInterface::SCHEMA_PROFILE_EVENT_ITEM_DATA => $subEvent[SchemaInterface::KEY_MAIN]
                            ];
                        }
                    }
                } catch (Throwable $e) {
                    $this->helper->logErrorMsg(__METHOD__, $e);
                    continue;
                }
            }
        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
        }
        return $eventsArr;
    }
}
