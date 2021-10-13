<?php

namespace Apsis\One\Helper;

use PrestaShop\PrestaShop\Adapter\CoreException;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityManager;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use Apsis\One\Model\Profile;
use Apsis\One\Model\Profile\Schema;
use Apsis\One\Model\Profile\DataProvider;
use Apsis\One\Model\Event;
use Apsis\One\Model\AbandonedCart;
use Apsis\One\Repository\ProfileRepository;
use Apsis\One\Repository\EventRepository;
use Apsis\One\Repository\AbandonedCartRepository;
use Apsis\One\Module\AbstractSetup;
use Validate;
use Context;
use Customer;
use Db;
use Throwable;

class EntityHelper extends LoggerHelper
{
    /**
     * @var ModuleHelper|null
     */
    protected $moduleHelper = null;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->moduleHelper = new ModuleHelper();
        parent::__construct();
    }

    /**
     * @param string $className
     * @return mixed
     *
     * @throws CoreException
     */
    public static function getRepository(string $className)
    {
        return ServiceLocator::get(EntityManager::class)->getRepository($className);
    }

    /**
     * @return ProfileRepository
     *
     * @throws CoreException
     */
    public function getProfileRepository(): ProfileRepository
    {
        $this->logInfoMsg(__METHOD__);
        return static::getRepository(Profile::class);
    }

    /**
     * @return EventRepository
     *
     * @throws CoreException
     */
    public function getEventRepository(): EventRepository
    {
        $this->logInfoMsg(__METHOD__);
        return static::getRepository(Event::class);
    }

    /**
     * @return AbandonedCartRepository
     *
     * @throws CoreException
     */
    public function getAbandonedCartRepository(): AbandonedCartRepository
    {
        $this->logInfoMsg(__METHOD__);
        return static::getRepository(AbandonedCart::class);
    }

    /**
     * VALID RFC 4211 COMPLIANT Universally Unique Identifier (UUID) version 4
     * https://www.php.net/manual/en/function.uniqid.php#94959
     *
     * @return string
     */
    public static function generateUniversallyUniqueIdentifier(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * @param Customer $customer
     */
    public function createProfileForCustomerEntity(Customer $customer): void
    {
        try {
            $profile = new Profile();
            $profile->setIdShop($customer->id_shop)
                ->setEmail($customer->email)
                ->setIdCustomer($customer->id)
                ->setIsCustomer(true)
                ->setIsGuest((bool) $customer->is_guest)
                ->setIsOffers((bool) $customer->optin)
                ->setIsNewsletter((bool) $customer->newsletter)
                ->add(true);
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param string $email
     * @param int $emailSubscriptionId
     */
    public function createProfileForEmailSubscriptionEntity(string $email, int $emailSubscriptionId): void
    {
        try {
            $profile = new Profile();
            $profile->setEmail($email)
                ->setIdShop(Context::getContext()->shop->id)
                ->setIdNewsletter($emailSubscriptionId)
                ->setIsNewsletter(true)
                ->add(true);
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param Profile $profile
     * @param Customer $customer
     */
    public function updateProfileForCustomerEntity(Profile $profile, Customer $customer): void
    {
        try {
            $profile->setIdCustomer($customer->id)
                ->setIsCustomer(true)
                ->setIsGuest((bool) $customer->is_guest)
                ->setIsOffers((bool) $customer->optin)
                ->setIdNewsletter()
                ->setIsNewsletter((bool) $customer->newsletter)
                ->setSyncStatus()
                ->update(true);
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param Profile $profile
     * @param int $emailSubscriptionId
     */
    public function updateProfileForEmailSubscriptionEntity(Profile $profile, int $emailSubscriptionId): void
    {
        try {
            $profile->setIdNewsletter($emailSubscriptionId)
                ->setIsNewsletter(true)
                ->setIdCustomer()
                ->setIsCustomer()
                ->setIsOffers()
                ->setIsGuest()
                ->setSyncStatus()
                ->update(true);
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param string $email
     * @param int $shopId
     *
     * @return int|null
     */
    public function getEmailSubscriptionIdByEmailAndShop(string $email, int $shopId): ?int
    {
        try {
            $sql = sprintf(
                "SELECT `id` FROM %s WHERE `email` = '%s' AND id_shop = %d",
                AbstractSetup::getTableWithDbPrefix('emailsubscription'), pSQL($email), $shopId
            );

            return $this->fetchSingleValueFromRow($sql, 'integer');
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @param string $email
     * @param int $shopId
     * @param bool $isEmailSubscriber
     *
     * @return int|null
     */
    public function getCustomerIdByEmailAndShop(string $email, int $shopId, bool $isEmailSubscriber = true): ?int
    {
        try {
            $sql = sprintf(
                "SELECT `id_customer` FROM %s WHERE `email` = '%s' AND id_shop = %d",
                AbstractSetup::getTableWithDbPrefix('customer'), pSQL($email), $shopId
            );

            if ($isEmailSubscriber) {
                $sql .= ' AND `newsletter` = 1';
            }

            return $this->fetchSingleValueFromRow($sql, 'integer');
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @param int $id
     *
     * @return Customer|null
     */
    public function getCustomerById(int $id): ?Customer
    {
        $customer = new Customer($id);
        if (Validate::isLoadedObject($customer)) {
            return $customer;
        }

        return null;
    }

    /**
     * @param string $sql
     * @param string $type
     *
     * @return mixed|null
     */
    public static function fetchSingleValueFromRow(string $sql, string $type)
    {
        if (is_array($row = Db::getInstance()->getRow($sql, false))) {
            $value = array_shift($row);
            settype($value, $type);
            return $value;
        }

        return null;
    }

    /**
     * @param Profile $profile
     *
     * @return array|null
     */
    public function getProfileDataArrForExport(Profile $profile): ?array
    {
        $profileData = $profile->getProfileDataArr();
        if (empty($profileData)) {
            return null;
        }

        /** @var Schema $schemaProvider */
        $schemaProvider = $this->moduleHelper->getService(HelperInterface::SERVICE_PROFILE_SCHEMA);
        /** @var DataProvider $dataProvider */
        $dataProvider = $this->moduleHelper->getService(HelperInterface::SERVICE_PROFILE_CONTAINER);

        try {
            return $dataProvider->setObjectData($profileData, $schemaProvider)->getDataArr();
        } catch (Throwable $e) {
            $this->moduleHelper->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }
}
