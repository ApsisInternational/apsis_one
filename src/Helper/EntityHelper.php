<?php

namespace Apsis\One\Helper;

use Apsis\One\Context\LinkContext;
use Apsis\One\Context\ShopContext;
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
use PrestaShop\PrestaShop\Adapter\Database;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityManager\QueryBuilder;
use Cart;
use mysqli;
use Order;
use PDOStatement;
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
        return static::getRepository(Profile::class);
    }

    /**
     * @return EventRepository
     *
     * @throws CoreException
     */
    public function getEventRepository(): EventRepository
    {
        return static::getRepository(Event::class);
    }

    /**
     * @return AbandonedCartRepository
     *
     * @throws CoreException
     */
    public function getAbandonedCartRepository(): AbandonedCartRepository
    {
        return static::getRepository(AbandonedCart::class);
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
     * @param array $rows
     *
     * @return int
     */
    public function registerSubsEventsForSubscribers(array $rows): int
    {
        $eventsCreated = 0;
        foreach ($rows as $row) {
            try {
                if (isset($row['id_newsletter'], $row['newsletter'], $row['id_apsis_profile'], $row['id_shop'])) {
                    $this->registerSubsEventsForSubscriber(
                        $row['id_apsis_profile'],
                        $row['id_newsletter'],
                        (1 === (int) $row['newsletter']) ? EI::ET_NEWS_GUEST_OPTIN : EI::ET_NEWS_GUEST_OPTOUT,
                        $row['id_shop']
                    );
                    $eventsCreated++;
                }
            } catch (Throwable $e) {
                $this->logErrorMsg(__METHOD__, $e);
                continue;
            }
        }
        return $eventsCreated;
    }

    /**
     * @param int $profileId
     * @param int $subscriberId
     * @param int $eventType
     * @param int|null $shopId
     */
    public function registerSubsEventsForSubscriber(
        int $profileId,
        int $subscriberId,
        int $eventType,
        ?int $shopId = null
    ): void {
        try {
            if (is_null($shopId)) {
                $shopId = $this->shopContext->getCurrentShopId();
            }

            $shopGroupId = $this->shopContext->getGroupIdFromShopId($shopId);
            $jsonData = json_encode([
                'id_subscriber' => $subscriberId,
                'id_shop' => $shopId,
                'id_shop_group' => $shopGroupId,
                'shop_name' => $this->shopContext->getShopNameById($shopId),
                'shop_group_name' => $this->shopContext->getShopGroupNameById($shopGroupId),
                'ipRegistrationNewsletter' => $this->getIpAddressFromEmailSubscription($subscriberId),
            ]);
            $this->registerEvent($profileId, $shopId, $subscriberId, $eventType, $jsonData);
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param array $rows
     *
     * @return int
     */
    public function registerSubsEventsForCustomers(array $rows): int
    {
        $eventsCreated = 0;
        foreach ($rows as $row) {
            try {
                if (isset($row['id_customer'], $row['newsletter'], $row['id_apsis_profile'])) {
                    $this->registerEventForCustomer(
                        new Customer((int) $row['id_customer']),
                        $row['id_apsis_profile'],
                        (1 === (int) $row['newsletter']) ? EI::ET_CUST_SUB_NEWS : EI::ET_CUST_UNSUB_NEWS
                    );
                    $eventsCreated++;
                }
            } catch (Throwable $e) {
                $this->logErrorMsg(__METHOD__, $e);
                continue;
            }
        }
        return $eventsCreated;
    }

    /**
     * @param Customer $customer
     * @param int $profileId
     * @param int $eventType
     * @param int $subscriberId
     */
    public function registerEventForCustomer(
        Customer $customer,
        int $profileId,
        int $eventType,
        int $subscriberId = 0
    ): void {
        try {
            $jsonData = json_encode([
                'id_customer' => $customer->id,
                'id_subscriber' => $subscriberId,
                'id_shop' => $customer->id_shop,
                'id_shop_group' => $customer->id_shop_group,
                'shop_name' => $this->shopContext->getShopNameById($customer->id_shop),
                'shop_group_name' => $this->shopContext->getShopGroupNameById($customer->id_shop_group),
                'ipRegistrationNewsletter' => $customer->ip_registration_newsletter,
            ]);
            $this->registerEvent($profileId, $customer->id_shop, $customer->id, $eventType, $jsonData);
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
            if (isset($hookArgs['operator']) && $hookArgs['operator'] === 'up' && ! empty($hookArgs['quantity']) &&
                isset($hookArgs['cart'], $hookArgs['product'], $hookArgs['shop'])
            ) {
                $cart = $hookArgs['cart'];
                $product = $hookArgs['product'];
                $shop = $hookArgs['shop'];

                if ($cart instanceof Cart && $product instanceof Product && $shop instanceof Shop && $cart->id_customer
                    && $profile = $this->getProfileRepository()->findOneByCustomerId($cart->id_customer)
                ) {
                    $jsonData = json_encode([
                        'id_cart' => $cart->id,
                        'id_lang' => $cart->id_lang,
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
                        'currency_code' => $this->moduleHelper->getCurrencyIsoCodeById($cart->id_currency)
                    ]);
                    $this->registerEvent(
                        $profile->getId(),
                        $cart->id_shop,
                        $cart->id_customer,
                        EI::ET_PRODUCT_CARTED,
                        $jsonData
                    );
                }
            }
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param array $hookArgs
     */
    public function registerProductReviewEvent(array $hookArgs): void
    {
        try {
            if (isset($hookArgs['object']) && is_object($object = $hookArgs['object']) && isset($object->id) &&
                isset($object->validate) && $object->validate
            ) {
                $partSql = sprintf(EI::EVENT_REVIEW_PRODUCT_SQL, EI::SS_PENDING);
                $partWhere = sprintf(
                    EI::EVENT_REVIEW_PRODUCT_SQL_COND,
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
        try {
            if (! empty($hookArgs['idWishlist']) &&
                ! empty($hookArgs['customerId']) &&
                ! empty($hookArgs['idProduct'])
            ) {
                $partSql = sprintf(EI::EVENT_WISHLIST_PRODUCT_SQL, EI::SS_PENDING);
                $where = sprintf(
                    EI::EVENT_WISHLIST_PRODUCT_SQL_COND,
                    (int) $hookArgs['idWishlist'],
                    (int) $hookArgs['idProduct'],
                    (int) $hookArgs['customerId']
                );
                Db::getInstance()->query($partSql . $where);

            }
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param array $hookArgs
     */
    public function registerOrderPlacedEvent(array $hookArgs): void
    {
        try {
            if (isset($hookArgs['object']) && is_object($object = $hookArgs['object']) && $object instanceof Order &&
                isset($object->id) && isset($object->id_customer) &&
                ! empty($profile = $this->getProfileRepository()->findOneByCustomerId($object->id_customer)) &&
                $profile instanceof Profile
            ) {
                $partSql = sprintf(EI::EVENT_ORDER_INSERT_SQL, EI::SS_PENDING);
                $where = sprintf(EI::EVENT_ORDER_INSERT_SQL_COND, (int) $object->id);
                $result = Db::getInstance()->query($partSql . $where);

                if (($result instanceof PDOStatement && $result->rowCount()) ||
                    ($result instanceof mysqli && $result->affected_rows)
                ) {
                    $this->updateProfileForCustomerEntity($profile, new Customer($object->id_customer));
                }
            }
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param int $profileId
     * @param int $shopId
     * @param int $psEntityId
     * @param int $eventType
     * @param string $eventData
     */
    public function registerEvent(
        int $profileId,
        int $shopId,
        int $psEntityId,
        int $eventType,
        string $eventData
    ) {
        try {
            $event = new Event();
            $event->setIdApsisProfile($profileId)
                ->setIdShop($shopId)
                ->setIdEntityPs($psEntityId)
                ->setEventType($eventType)
                ->setEventData($eventData)
                ->add();
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param Profile $profile
     * @param bool $fetchEvents
     *
     * @return array|null
     */
    public function getProfileDataArrForExport(Profile $profile, bool $fetchEvents): ?array
    {
        try {
            $profileData = $profile->getProfileDataArr();
            if (empty($profileData)) {
                return null;
            }

            if ($fetchEvents) {
                $events = $this->getEventRepository()
                    ->findByProfileIdAndSyncStatus($profile->getId(), [EI::SS_JUSTIN]);
                $profileData[SchemaInterface::PROFILE_SCHEMA_TYPE_EVENTS] = is_null($events) ? [] : $events;
            } else {
                $profileData[SchemaInterface::PROFILE_SCHEMA_TYPE_EVENTS] = [];
            }

            /** @var SchemaInterface $schemaProvider */
            $schemaProvider = $this->moduleHelper->getService(HelperInterface::SERVICE_PROFILE_SCHEMA);
            /** @var DataInterface $dataProvider */
            $dataProvider = $this->moduleHelper->getService(HelperInterface::SERVICE_PROFILE_CONTAINER);

            return $dataProvider->setObjectData($profileData, $schemaProvider)->getDataArr();
        } catch (Throwable $e) {
            $this->moduleHelper->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @param Event $event
     * @param string $dtFormat
     *
     * @return array|null
     */
    public function getEventDataArrForExport(Event $event, string $dtFormat = self::TIMESTAMP): ?array
    {
        try {
            if (empty($eventData =  json_decode($event->getEventData(), true)) || empty($event->getEventType())) {
                return null;
            }

            if (in_array($event->getEventType(), SchemaInterface::EVENTS_CONTAINING_PRODUCT)) {
                $eventData = $this->setProductDataInArr($eventData);
            }

            /** @var SchemaInterface $schemaProvider */
            $schemaProvider = $this->moduleHelper
                ->getService(SchemaInterface::EVENT_TYPE_TO_SCHEMA_MAP[$event->getEventType()]);
            /** @var DataInterface $dataProvider */
            $dataProvider = $this->moduleHelper->getService(HelperInterface::SERVICE_EVENT_CONTAINER);

            $eventDataArr = $dataProvider->setObjectData($eventData, $schemaProvider)->getDataArr();
            if (empty($eventDataArr[SchemaInterface::KEY_MAIN])) {
                return null;
            }

            if (isset($eventDataArr[SchemaInterface::KEY_ITEMS])) {
                $subEvents = $eventDataArr[SchemaInterface::KEY_ITEMS];
                unset($eventDataArr[SchemaInterface::KEY_ITEMS]);
            }

            /** @var DateHelper $dateHelper */
            $dateHelper = $this->moduleHelper->getService(HelperInterface::SERVICE_HELPER_DATE);

            $discriminator = SchemaInterface::EVENT_TYPE_TO_DISCRIMINATOR_MAP[$event->getEventType()];
            $createdAt = $dateHelper->formatDate($dtFormat, $event->getDateAdd(), $event->getIdShop(), self::TZ_UTC);
            $eventsArr[$event->getId()] = [
                SchemaInterface::SCHEMA_PROFILE_EVENT_ITEM_TIME => $createdAt,
                SchemaInterface::SCHEMA_PROFILE_EVENT_ITEM_DISCRIMINATOR =>
                    is_array($discriminator) ? $discriminator[SchemaInterface::KEY_MAIN] : $discriminator,
                SchemaInterface::SCHEMA_PROFILE_EVENT_ITEM_DATA => $eventDataArr[SchemaInterface::KEY_MAIN]
            ];

            if (is_array($discriminator) && ! empty($subEvents)) {
                $withAddedSecond = $createdAt;
                foreach ($subEvents as $index => $subEvent) {
                    if (empty($subEvent[SchemaInterface::KEY_MAIN])) {
                        continue;
                    }

                    $eventsArr['p' . $event->getId() . 'c' . $index] = [
                        SchemaInterface::SCHEMA_PROFILE_EVENT_ITEM_TIME =>
                            $withAddedSecond = $dateHelper->addSecond($withAddedSecond, $dtFormat),
                        SchemaInterface::SCHEMA_PROFILE_EVENT_ITEM_DISCRIMINATOR =>
                            $discriminator[SchemaInterface::KEY_ITEMS],
                        SchemaInterface::SCHEMA_PROFILE_EVENT_ITEM_DATA => $subEvent[SchemaInterface::KEY_MAIN]
                    ];
                }
            }

            return $eventsArr;
        } catch (Throwable $e) {
            $this->moduleHelper->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @param AbandonedCart $abandonedCart
     *
     * @return array|null
     */
    public function getAbandonedCartDataArrForExport(AbandonedCart $abandonedCart): ?array
    {
        try {
            if (empty($dataArr = $abandonedCart->getCartData())) {
                return null;
            }

            $dataArr = $this->setProductDataInArr(json_decode($dataArr, true));
            $dataArr[EI::C_TOKEN] = $abandonedCart->getToken();

            /** @var SchemaInterface $schemaProvider */
            $schemaProvider = $this->moduleHelper->getService(HelperInterface::SERVICE_ABANDONED_CART_SCHEMA);
            /** @var DataInterface $dataProvider */
            $dataProvider = $this->moduleHelper->getService(HelperInterface::SERVICE_ABANDONED_CART_CONTAINER);

            $formattedDataArr = $dataProvider->setObjectData($dataArr, $schemaProvider)->getDataArr();
            if (empty($formattedDataArr[SchemaInterface::KEY_MAIN]) ||
                empty($formattedDataArr[SchemaInterface::KEY_ITEMS])
            ) {
                return null;
            }

            $sortedDataArr = $formattedDataArr[SchemaInterface::KEY_MAIN];
            foreach ($formattedDataArr[SchemaInterface::KEY_ITEMS] as $item) {
                if (isset($item[SchemaInterface::KEY_MAIN])) {
                    $sortedDataArr[SchemaInterface::KEY_ITEMS][] = $item[SchemaInterface::KEY_MAIN];
                }
            }

            return $sortedDataArr;
        } catch (Throwable $e) {
            $this->moduleHelper->logErrorMsg(__METHOD__, $e);
            return null;
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
            if (isset($arr['id_product'], $arr['id_lang'], $arr['id_shop'])) {
                $product = new Product($arr['id_product'], true, $arr['id_lang'], $arr['id_shop']);
                if (Validate::isLoadedObject($product)) {
                    $arr['product_image_url'] = $this->linkContext->getProductCoverImage($product);
                    $arr['product_url'] = $this->linkContext->getProductLink($product, $arr['id_shop'], $arr['id_lang']);
                    $arr['product_price_amount_incl_tax'] = $product->getPrice();
                    $arr['product_price_amount_excl_tax'] = $product->getPrice(false);
                }
            } elseif (isset($arr[SchemaInterface::KEY_ITEMS]) && is_array($arr[SchemaInterface::KEY_ITEMS])) {
                $arr['items_count'] = count($arr[SchemaInterface::KEY_ITEMS]);
                $arr['total_product_incl_tax'] = $arr['total_product_excl_tax'] = 0;
                foreach ($arr[SchemaInterface::KEY_ITEMS] as $key => $itemArr) {
                    if (isset($arr['id_lang'], $arr['id_shop'])) {
                        $itemArr['id_lang'] = $arr['id_lang'];
                        $itemArr['id_shop'] = $arr['id_shop'];

                        $itemArr = $this->setProductDataInArr($itemArr);

                        $arr[SchemaInterface::KEY_ITEMS][$key] = $itemArr;
                        $arr['total_product_incl_tax'] += $itemArr['product_price_amount_incl_tax'] ?? null;
                        $arr['total_product_excl_tax'] += $itemArr['product_price_amount_excl_tax'] ?? null;
                    }
                }
            }

        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
        return $arr;
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
     * @param string $table
     * @param int $status
     * @param array $ids
     * @param string $msg
     *
     * @return int|null
     */
    public function updateStatusForEntityByIds(string $table, int $status, array $ids, string $msg = ''): ?int
    {
        try {
            $queryBuilder = new QueryBuilder(new Database());
            $result = Db::getInstance()->query(
                sprintf(
                    "UPDATE `%s` SET `%s` = %d, `%s` = %s WHERE %s",
                    _DB_PREFIX_ . $table,
                    EI::C_SYNC_STATUS,
                    $status,
                    EI::C_ERROR_MSG,
                    $msg,
                    $queryBuilder->buildWhereConditions('AND', [EI::T_PRIMARY_MAPPINGS[$table] => $ids])
                )
            );
            if (($result instanceof PDOStatement && $rowCount = $result->rowCount()) ||
                ($result instanceof mysqli && $rowCount = $result->affected_rows)
            ) {
                return $rowCount;
            }
        } catch (Throwable $e) {
            $this->moduleHelper->logErrorMsg(__METHOD__, $e);
        }

        return null;
    }
}
