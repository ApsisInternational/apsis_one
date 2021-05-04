<?php

namespace Apsis\One\Model\Profile;

use Apsis\One\Helper\LoggerHelper;
use Exception;

class Data
{
    const ENTRY_ID_FIELD_NAME = 'entryId';

    /**
     * @var LoggerHelper
     */
    protected $loggerHelper;

    /**
     * @var array
     */
    private $schema;

    /**
     * @var object
     */
    private $object;

    /**
     * @var array
     */
    private $profileData = [];

    /**
     * Install constructor.
     *
     * @param LoggerHelper $loggerHelper
     * @param Schema $schema
     */
    public function __construct(
        LoggerHelper $loggerHelper,
        Schema $schema
    ) {
        $this->loggerHelper = $loggerHelper;
        $this->schema = $schema->getProfileSchema();
    }

    /**
     * @param $object
     *
     * @return $this
     *
     * @throws Exception
     */
    public function setObject($object)
    {
        $this->profileData = [];
        $this->object = $object;

        foreach (Schema::SCHEMA_TYPES as $schemaType) {
            $this->profileData[$schemaType] =
                $this->setProfileDataBySchemaType($schemaType, $this->schema[$schemaType]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getProfileData()
    {
        return $this->profileData;
    }

    /**
     * @param string $schemaType
     * @param array $schema
     *
     * @return array|string
     *
     * @throws Exception
     */
    private function setProfileDataBySchemaType(string $schemaType, array $schema)
    {
        if ($schemaType === Schema::SCHEMA_TYPE_ENTRY) {
            return $this->getValue(
                self::ENTRY_ID_FIELD_NAME,
                $schema[Schema::SCHEMA_KEY_TYPE],
                $schema[Schema::SCHEMA_KEY_VALIDATE]
            );
        }

        $items = [];
        foreach ($schema as $schemaItem) {
            $logicalName = $schemaItem[Schema::SCHEMA_KEY_LOGICAL_NAME];
            $items[] = [
                $logicalName => $this->getValue(
                    $logicalName,
                    $schemaItem[Schema::SCHEMA_KEY_TYPE],
                    $schemaItem[Schema::SCHEMA_KEY_VALIDATE]
                )
            ];
        }

        return $items;
    }

    /**
     * @param string $logicalName
     * @param string $type
     * @param string $validate
     *
     * @return mixed
     *
     * @throws Exception
     */
    private function getValue(string $logicalName, string $type, string $validate)
    {
        $function = 'get' . ucfirst($logicalName);
        $value = call_user_func(['self', $function]);
        return $this->validateAndFormat($value, $type, $validate);
    }

    /**
     * @param mixed $value
     * @param string $type
     * @param string $validate
     *
     * @return mixed
     *
     * @throws Exception
     */
    private function validateAndFormat($value, string $type, string $validate)
    {
        if (in_array($validate, Schema::NOT_NULL_VALIDATIONS) && empty($value)) {
            $msg = __METHOD__ . " - Invalid value (" . var_export($value, true) . ") for validation ($validate)";
            throw new Exception($msg);
        }

        if (empty($value)) {
            return null;
        }

        settype($value, $type);

        //@toDo do the validation based on validate type

        return $value;
    }

    /**
     * @return mixed
     */
    private function getEntryId()
    {
       return $this->getProfileId();
    }

    private function getProfileId()
    {
        //return $this->object->getSomeValue();
        return $this->object->profileId;
    }

    private function getCustomerId()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getSubscriptionId()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getShopId()
    {
        //return $this->object->getSomeValue();
        return $this->object->shopId;
    }

    private function getShopGroupId()
    {
        //return $this->object->getSomeValue();
        return $this->object->shopGroupId;
    }

    private function getShopName()
    {
        //return $this->object->getSomeValue();
        return $this->object->shopName;
    }

    private function getShopGroupName()
    {
        //return $this->object->getSomeValue();
        return $this->object->shopGroupName;
    }

    private function getCustomerGroup()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getIsSubscribedToNewsletter()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getNewsletterDateAdded()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getIsGuest()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getIsActive()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getLanguageName()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getDateAdded()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getFirstName()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getLastName()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getAlias()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getEmail()
    {
        //return $this->object->getSomeValue();
        return $this->object->email;
    }

    private function getGender()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getBirthday()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getCompany()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getBillingAddress1()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getBillingAddress2()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getBillingPostcode()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getBillingCity()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getBillingPhone()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getBillingPhoneMobile()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getBillingState()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getBillingCountry()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getShippingAddress1()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getShippingAddress2()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getShippingPostcode()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getShippingCity()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getShippingPhone()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getShippingPhoneMobile()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getShippingState()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getShippingCountry()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getLifetimeTotalSpend()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    private function getNewsletterSubscription()
    {
        //return $this->object->getSomeValue();
        return $this->getIsSubscribedToNewsletter();
    }
}