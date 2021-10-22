<?php

namespace Apsis\One\Helper;

use Apsis\One\Context\LinkContext;
use Apsis\One\Context\ShopContext;
use Cart;
use Currency;
use PrestaShop\PrestaShop\Adapter\CoreException;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityManager;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use Apsis\One\Model\EntityInterface as EI;
use Apsis\One\Model\Profile;
use Apsis\One\Model\SchemaInterface;
use Apsis\One\Model\DataInterface;
use Apsis\One\Model\Event;
use Apsis\One\Model\AbandonedCart;
use Apsis\One\Module\AbstractSetup;
use Apsis\One\Repository\ProfileRepository;
use Apsis\One\Repository\EventRepository;
use Apsis\One\Repository\AbandonedCartRepository;
use Product;
use Shop;
use Validate;
use Customer;
use Db;
use Throwable;

class EntityHelper extends LoggerHelper
{
    /**
     * @var ModuleHelper
     */
    protected $moduleHelper;

    /**
     * @var ShopContext
     */
    protected $shopContext;

    /**
     * @var LinkContext|null
     */
    protected $linkContext;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->moduleHelper = new ModuleHelper();
        $this->shopContext = $this->moduleHelper->getService(self::SERVICE_CONTEXT_SHOP);
        $this->linkContext = $this->moduleHelper->getService(HelperInterface::SERVICE_CONTEXT_LINK);
        parent::__construct();
    }

    /**
     * @param string $className
     *
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
     *
     * @return Profile|null
     */
    public function createProfileForCustomerEntity(Customer $customer): ?Profile
    {
        try {
            $profile = new Profile();
            $profile->setIdShop($customer->id_shop)
                ->setEmail($customer->email)
                ->setIdCustomer($customer->id)
                ->setIsCustomer(EI::YES)
                ->setIsGuest((bool) $customer->is_guest)
                ->setIsOffers((bool) $customer->optin)
                ->setIsNewsletter((bool) $customer->newsletter)
                ->add(true);

            //Register event(s), if any.
            $this->registerSubsEventsForCustomer(new Profile(), $customer, $profile->getId());

            return $profile;
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @param string $email
     * @param int $emailSubscriptionId
     *
     * @return Profile|null
     */
    public function createProfileForEmailSubscriptionEntity(string $email, int $emailSubscriptionId): ?Profile
    {
        try {
            $profile = new Profile();
            $profile->setEmail($email)
                ->setIdShop($this->shopContext->getCurrentShopId())
                ->setIdNewsletter($emailSubscriptionId)
                ->setIsNewsletter(EI::YES)
                ->add(true);

            // Event: New Non customer user subscribes to newsletter
            $this->registerSubsEventsForSubscriber($profile->getId(), $emailSubscriptionId, EI::ET_NEWS_GUEST_OPTIN);

            return $profile;
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @param Profile $profile
     * @param Customer $customer
     */
    public function updateProfileForCustomerEntity(Profile $profile, Customer $customer): void
    {
        try {
            //Register event(s), if any.
            $this->registerSubsEventsForCustomer($profile, $customer);

            // Update profile
            $profile->setIdCustomer($customer->id)
                ->setIsCustomer(EI::YES)
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
     * @param bool $subscriber
     */
    public function updateProfileForEmailSubscriptionEntity(
        Profile $profile,
        int $emailSubscriptionId,
        bool $subscriber = EI::YES
    ): void {
        try {
            $profile->setIdNewsletter($emailSubscriptionId)
                ->setIsNewsletter($subscriber)
                ->setIdCustomer()
                ->setIsCustomer()
                ->setIsOffers()
                ->setIsGuest()
                ->setSyncStatus($emailSubscriptionId ? EI::SS_PENDING : EI::SS_NOTHING)
                ->update(true);
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param Profile $profile
     * @param Customer $customer
     * @param int $profileId
     */
    protected function registerSubsEventsForCustomer(Profile $profile, Customer $customer, int $profileId = 0): void {
        try {
            // Newsletter subscriber is customer
            if ($profile->getIsNewsletter() && $profile->getIdNewsletter() && $customer->newsletter) {
                $this->registerEventForCustomer(
                    $customer,
                    $profileId ?: $profile->getId(),
                    EI::ET_NEWS_SUB_2_CUST,
                    $profileId ? 0 : $profile->getIdNewsletter()
                );
            }

            // Customer subscribes to Newsletter
            if (! $profile->getIsNewsletter() && $customer->newsletter) {
                $this->registerEventForCustomer($customer, $profileId ?: $profile->getId(), EI::ET_CUST_SUB_NEWS);
            }
            // Customer unsubscribes from Newsletter
            elseif ($profile->getIsNewsletter() && ! $customer->newsletter) {
                $this->registerEventForCustomer($customer, $profileId ?: $profile->getId(), EI::ET_CUST_UNSUB_NEWS);
            }

            // Customer subscribes to partner offers
            if (! $profile->getIsOffers() && $customer->optin) {
                $this->registerEventForCustomer($customer, $profileId ?: $profile->getId(), EI::ET_CUST_SUB_OFFERS);
            }
            // Customer unsubscribes from partner offers
            elseif ($profile->getIsOffers() && ! $customer->optin) {
                $this->registerEventForCustomer($customer, $profileId ?: $profile->getId(), EI::ET_CUST_UNSUB_OFFERS);
            }
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param int $profileId
     * @param int $subscriberId
     * @param int $eventType
     */
    public function registerSubsEventsForSubscriber(int $profileId, int $subscriberId, int $eventType): void
    {
        try {
            if (isset(SchemaInterface::EVENT_TYPE_TO_SCHEMA_MAP[$eventType])) {
                $shopId = $this->shopContext->getCurrentShopId();
                $shopGroupId = $this->shopContext->getCurrentShopGroupId();
                $dataArr = $this->getEventDataArrForExport(
                    [
                        'id_subscriber' => $subscriberId,
                        'id_shop' => $shopId,
                        'id_shop_group' => $shopGroupId,
                        'shop_name' => $this->shopContext->getShopNameById($shopId),
                        'shop_group_name' => $this->shopContext->getShopGroupNameById($shopGroupId),
                        'ipRegistrationNewsletter' => $this->getIpAddressFromEmailSubscription($subscriberId),
                    ],
                    SchemaInterface::EVENT_TYPE_TO_SCHEMA_MAP[$eventType]
                );
                if (! empty($dataArr[SchemaInterface::KEY_MAIN])) {
                    $jsonData = json_encode($dataArr[SchemaInterface::KEY_MAIN]);
                    $this->registerEvent($profileId, $shopId, $subscriberId, $eventType, $jsonData);
                }
            }
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param Customer $customer
     * @param int $profileId
     * @param int $eventType
     * @param int $subscriberId
     */
    public function registerEventForCustomer(Customer $customer, int $profileId, int $eventType, int $subscriberId = 0): void
    {
        try {
            if (isset(SchemaInterface::EVENT_TYPE_TO_SCHEMA_MAP[$eventType])) {
                $dataArr = $this->getEventDataArrForExport(
                    [
                        'id_customer' => $customer->id,
                        'id_subscriber' => $subscriberId,
                        'id_shop' => $customer->id_shop,
                        'id_shop_group' => $customer->id_shop_group,
                        'shop_name' => $this->shopContext->getShopNameById($customer->id_shop),
                        'shop_group_name' => $this->shopContext->getShopGroupNameById($customer->id_shop_group),
                        'ipRegistrationNewsletter' => $customer->ip_registration_newsletter,
                    ],
                    SchemaInterface::EVENT_TYPE_TO_SCHEMA_MAP[$eventType]
                );
                if (! empty($dataArr[SchemaInterface::KEY_MAIN])) {
                    $jsonData = json_encode($dataArr[SchemaInterface::KEY_MAIN]);
                    $this->registerEvent($profileId, $customer->id_shop, $customer->id, $eventType, $jsonData);
                }
            }
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param array $hookArgs
     */
    public function registerProductCartedEvent(array $hookArgs): void
    {
        try {
            $this->logInfoMsg(__METHOD__);

            if (isset($hookArgs['operator']) && $hookArgs['operator'] === 'up' && ! empty($hookArgs['quantity']) &&
                isset($hookArgs['cart'], $hookArgs['product'], $hookArgs['shop'])
            ) {
                $cart = $hookArgs['cart'];
                $product = $hookArgs['product'];
                $shop = $hookArgs['shop'];

                if ($cart instanceof Cart && $product instanceof Product && $shop instanceof Shop &&
                    $profile = $this->getProfileRepository()->findOneByCustomerId($cart->id_customer)
                ) {
                    $dataArr = $this->getEventDataArrForExport(
                        [
                            'id_cart' => $cart->id,
                            'id_customer' => $cart->id_customer,
                            'guest_id' => $cart->id_guest,
                            'id_shop' => $cart->id_shop,
                            'id_shop_group' => $cart->id_shop_group,
                            'id_product' => $product->id,
                            'shop_name' => $shop->name,
                            'shop_group_name' => $this->shopContext->getShopGroupNameById($cart->id_shop_group),
                            'product_reference' => $product->reference,
                            'product_name' => $product->name,
                            'product_qty' => (int) $hookArgs['quantity'],
                            'product_url' => $product->getLink(),
                            'product_image_url' => $this->linkContext->getProductCoverImage($product),
                            'currency_code' => $this->getCurrencyIsoCodeById($cart->id_currency),
                            'product_price_amount_incl_tax' => $product->getPrice(),
                            'product_price_amount_excl_tax' => $product->getPrice(false)
                        ],
                        SchemaInterface::EVENT_TYPE_TO_SCHEMA_MAP[EI::ET_PRODUCT_CARTED]
                    );
                    if (! empty($dataArr[SchemaInterface::KEY_MAIN])) {
                        $this->registerEvent(
                            $profile->getId(),
                            $cart->id_shop,
                            $cart->id_customer,
                            EI::ET_PRODUCT_CARTED,
                            json_encode($dataArr[SchemaInterface::KEY_MAIN])
                        );
                    }
                }
            }
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param array $arr
     *
     * @return array
     */
    public function setProductDataInArr(array $arr): array
    {
        try {
            if (! empty($arr) && ! empty($arr['id_product']) && ! empty($arr['id_shop']) && ! empty($arr['id_lang'])) {
                $product = new Product($arr['id_product'], true, $arr['id_lang'], $arr['id_shop']);
                if (Validate::isLoadedObject($product)) {
                    $arr['product_image_url'] = $this->linkContext->getProductCoverImage($product);
                    $arr['product_url'] = $product->getLink();
                    $arr['product_price_amount_incl_tax'] = $product->getPrice();
                    $arr['product_price_amount_excl_tax'] = $product->getPrice(false);
                }
            }
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
        return $arr;
    }

    /**
     * @param array $hookArgs
     */
    public function registerProductReviewEvent(array $hookArgs): void
    {
        $this->logInfoMsg(__METHOD__);

        try {
            if (Validate::isLoadedObject($object = $hookArgs['object']) && $object->validate) {
                $partSql = sprintf(EI::EVENT_DATA_SQL_REVIEW_PRODUCT,EI::ET_PRODUCT_REVIEWED, EI::SS_PENDING);
                $partWhere = sprintf(
                    EI::EVENT_DATA_SQL_REVIEW_PRODUCT_COND,
                    (int) $object->id
                );
                Db::getInstance()->query($partSql . $partWhere);
            }
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param array $hookArgs
     */
    public function registerProductWishedEvent(array $hookArgs): void
    {
        $this->logInfoMsg(__METHOD__);

        try {
            if (isset($hookArgs['idWishlist']) &&
                isset($hookArgs['customerId']) &&
                isset($hookArgs['idProduct']) &&
                isset($hookArgs['idProductAttribute'])
            ) {
                $partSql = sprintf(EI::EVENT_DATA_SQL_WISHLIST_PRODUCT,EI::ET_PRODUCT_WISHED, EI::SS_PENDING);
                $where = sprintf(
                    EI::EVENT_DATA_SQL_WISHLIST_PRODUCT_COND,
                    (int) $hookArgs['idWishlist'],
                    (int) $hookArgs['idProduct'],
                    (int) $hookArgs['idProductAttribute'],
                    (int) $hookArgs['customerId']
                );
                Db::getInstance()->query($partSql . $where);

            }
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param array $data
     * @param int $eventType
     *
     * @return array
     */
    protected function getEventDataArrContainingProductDataForExport(array $data, int $eventType): array
    {
        try {
            if (! empty($data[EI::C_EVENT_DATA]) && ! empty($data[EI::C_ID_PROFILE]) &&
                ! empty($data[EI::C_ID_SHOP] && ! empty($data[EI::C_ID_ENTITY_PS]))
            ) {
                $dataArr = $this->getEventDataArrForExport(
                    $this->setProductDataInArr(json_decode($data[EI::C_EVENT_DATA], true)),
                    SchemaInterface::EVENT_TYPE_TO_SCHEMA_MAP[$eventType]
                );
                if (! empty($dataArr[SchemaInterface::KEY_MAIN])) {
                    return $dataArr[SchemaInterface::KEY_MAIN];
                }
            }
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }

        return $data;
    }

    /**
     * @param int $profileId
     * @param int $shopId
     * @param int $psEntityId
     * @param int $eventType
     * @param string $eventData
     * @param string $subEventData
     */
    public function registerEvent(
        int $profileId,
        int $shopId,
        int $psEntityId,
        int $eventType,
        string $eventData,
        string $subEventData = ''
    ) {
        try {
            $event = new Event();
            $event->setIdApsisProfile($profileId)
                ->setIdShop($shopId)
                ->setIdEntityPs($psEntityId)
                ->setEventType($eventType)
                ->setEventData($eventData)
                ->setSubEventData($subEventData)
                ->add();
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
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

        /** @var SchemaInterface $schemaProvider */
        $schemaProvider = $this->moduleHelper->getService(HelperInterface::SERVICE_PROFILE_SCHEMA);
        /** @var DataInterface $dataProvider */
        $dataProvider = $this->moduleHelper->getService(HelperInterface::SERVICE_PROFILE_CONTAINER);

        try {
            return $dataProvider->setObjectData($profileData, $schemaProvider)->getDataArr();
        } catch (Throwable $e) {
            $this->moduleHelper->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @param array $eventData
     * @param string $schemaServiceId
     *
     * @return array|null
     */
    public function getEventDataArrForExport(array $eventData, string $schemaServiceId): ?array
    {
        if (empty($eventData) || empty($schemaServiceId)) {
            return null;
        }

        /** @var SchemaInterface $schemaProvider */
        $schemaProvider = $this->moduleHelper->getService($schemaServiceId);
        /** @var DataInterface $dataProvider */
        $dataProvider = $this->moduleHelper->getService(HelperInterface::SERVICE_EVENT_CONTAINER);

        try {
            return $dataProvider->setObjectData($eventData, $schemaProvider)->getDataArr();
        } catch (Throwable $e) {
            $this->moduleHelper->logErrorMsg(__METHOD__, $e);
            return null;
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
     * @param int $subscriptionId
     *
     * @return string|null
     */
    public function getIpAddressFromEmailSubscription(int $subscriptionId): ?string
    {
        try {
            $sql = sprintf(
                "SELECT `ip_registration_newsletter` FROM %s WHERE `id` = '%s'",
                AbstractSetup::getTableWithDbPrefix('emailsubscription'),
                $subscriptionId
            );

            return $this->fetchSingleValueFromRow($sql, 'string');
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
    public function getCustomerIdByEmailAndShop(string $email, int $shopId, bool $isEmailSubscriber): ?int
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
     * @param int $idCurrency
     *
     * @return Currency|null
     */
    public function getCurrencyById(int $idCurrency): ?Currency
    {
        try {
            return new Currency($idCurrency);
        } catch (Throwable $e) {
            $this->moduleHelper->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @param int $idCurrency
     *
     * @return string|null
     */
    public function getCurrencyIsoCodeById(int $idCurrency): ?string
    {
        try {
            if ($currency = $this->getCurrencyById($idCurrency)) {
                return $currency->iso_code;
            }

            return null;
        } catch (Throwable $e) {
            $this->moduleHelper->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }
}
