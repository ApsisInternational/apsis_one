<?php

namespace Apsis\One\Model;

use Apsis\One\Helper\HelperInterface;
use Apsis\One\Helper\ModuleHelper;
use Exception;
use Throwable;

// TODO: validations
abstract class AbstractData implements DataInterface
{
    /**
     * @var ModuleHelper
     */
    protected $helper;

    /**
     * @var object
     */
    protected $object;

    /**
     * @var array
     */
    protected $data = [];

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
    public function setObject($object, SchemaInterface $schema): DataInterface
    {
        $this->data = [];
        $this->object = $object;
        $definition = $schema->getDefinition();
        foreach ($schema->getDefinitionTypes() as $schemaType) {
            $this->data[$schemaType] = $this->getDataByDefinitionType($schemaType, $definition[$schemaType]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): array
    {
        return $this->data;
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
        $function = 'get' . ucfirst($logicalName);
        $value = call_user_func(['self', $function]);
        return $this->validateAndFormat($value, $type, $validate);
    }

    /**
     * @param string $definitionType
     * @param array $definition
     *
     * @return array|string
     *
     * @throws Throwable
     */
    protected function getDataByDefinitionType(string $definitionType, array $definition)
    {
        if ($definitionType === SchemaInterface::PROFILE_SCHEMA_TYPE_ENTRY) {
            return $this->getValue(
                SchemaInterface::SCHEMA_ENTRY_ID_FIELD_NAME,
                $definition[SchemaInterface::SCHEMA_KEY_TYPE],
                $definition[SchemaInterface::SCHEMA_KEY_VALIDATE]
            );
        }

        if ($definitionType === SchemaInterface::KEY_ITEMS) {
            /** @var SchemaInterface $itemSchema */
            $itemSchema = $this->helper->getService($definition[SchemaInterface::KEY_SCHEMA]);
            /** @var DataInterface $itemContainer */
            $itemContainer = $this->helper->getService($definition[SchemaInterface::KEY_PROVIDER]);
            $items = [];
            foreach ($this->object->getItems() as $item) {
                $items[] = $itemContainer->setObject($item, $itemSchema)->getData();
            }
            return $items;
        }

        $data = [];
        foreach ($definition as $schemaItem) {
            $logicalName = $schemaItem[SchemaInterface::SCHEMA_KEY_LOGICAL_NAME];
            $data[] = [
                $logicalName => $this->getValue(
                    $logicalName,
                    $schemaItem[SchemaInterface::SCHEMA_KEY_TYPE],
                    $schemaItem[SchemaInterface::SCHEMA_KEY_VALIDATE]
                )
            ];
        }

        return $data;
    }

    /**
     * @param mixed $value
     * @param string $type
     * @param string $validate
     *
     * @return mixed
     *
     * @throws Throwable
     */
    protected function validateAndFormat($value, string $type, string $validate)
    {
        if (in_array($validate, AbstractSchema::NOT_NULL_VALIDATIONS) && empty($value)) {
            $msg = __METHOD__ . " - Invalid value (" . var_export($value, true) . ") for validation ($validate)";
            throw new Exception($msg);
        }

        if (empty($value)) {
            return null;
        }

        settype($value, $type);

        // TODO: do the validation based on validate type

        return $value;
    }

    protected function isNullOrUnsignedInt(){}
    protected function isUnsignedInt(){}
    protected function isNullOrUnsignedId(){}
    protected function isUnsignedId(){}
    protected function isNullOrCustomerName(){}
    protected function isNullOrGenericName(){}
    protected function isGenericName(){}
    protected function isNullOrAddress(){}
    protected function isNullOrPostCode(){}
    protected function isNullOrCityName(){}
    protected function isNullOrPhoneNumber(){}
    protected function isEmail(){}
    protected function isNullOrDateFormatTimestamp(){}
    protected function isIntegrationProfileId(){}
    protected function isNullOrSalesValue(){}
    protected function isNullOrBoolean(){}
    protected function isNullOrUrl(){}
    protected function isUrl(){}
    protected function isNullOrIpAddress(){}
    protected function IsCurrencyCode(){}
}