<?php

namespace Apsis\One\Entity;

use Apsis\One\Entity\Repository\ProfileRepository;
use Apsis\One\Entity\Collection\ProfileCollection;
use Apsis\One\Helper\EntityHelper;
use PrestaShopDatabaseException;
use PrestaShopException;
use Shop;

class Profile extends AbstractEntity
{
    /**
     * @var string
     */
    protected $id_integration;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var bool
     */
    protected $is_customer = self::NO;

    /**
     * @var bool
     */
    protected $is_guest = self::NO;

    /**
     * @var bool
     */
    protected $is_subscriber = self::NO;

    /**
     * {@inheritdoc}
     */
    public static $definition = [
        'table' => self::T_PROFILE,
        'primary' => self::T_PRIMARY_MAPPINGS[self::T_PROFILE],
        'fields' => self::T_COLUMNS_MAPPINGS[self::T_PROFILE],
        'associations' => [
            'shop' => [
                'type' => self::HAS_ONE,
                'field' => self::C_ID_SHOP,
                'object' => Shop::class,
                'association' => 'shop'
            ],
            'events' => [
                'type' => self::HAS_MANY,
                'field' => self::C_ID_EVENT,
                'object' => Event::class,
                'association' => self::T_EVENT
            ],
            'abandoned_carts' => [
                'type' => self::HAS_MANY,
                'field' => self::C_ID_AC,
                'object' => AbandonedCart::class,
                'association' => self::T_ABANDONED_CART
            ],
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public static function fetchCollectionClassName(): string
    {
        return ProfileCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    public static function fetchRepositoryClassName(): string
    {
        return ProfileRepository::class;
    }

    /**
     * @param bool $auto_date
     * @param false $null_values
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function add($auto_date = true, $null_values = false): bool
    {
        if (empty($this->getIdIntegration())) {
            $this->setIdIntegration(EntityHelper::generateUniversallyUniqueIdentifier());
        }

        return parent::add($auto_date, $null_values);
    }

    /**
     * @return string
     */
    public function getIdIntegration(): string
    {
        return $this->id_integration;
    }

    /**
     * @param string $uuid
     *
     * @return $this
     */
    public function setIdIntegration(string $uuid): Profile
    {
        $this->id_integration = $uuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): Profile
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsCustomer(): bool
    {
        return $this->is_customer;
    }

    /**
     * @param bool $isCustomer
     *
     * @return $this
     */
    public function setIsCustomer(bool $isCustomer = self::NO): Profile
    {
        $this->is_customer = $isCustomer;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsSubscriber(): bool
    {
        return $this->is_subscriber;
    }

    /**
     * @param bool $isSubscriber
     *
     * @return $this
     */
    public function setIsSubscriber(bool $isSubscriber = self::NO): Profile
    {
        $this->is_subscriber = $isSubscriber;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsGuest(): bool
    {
        return $this->is_guest;
    }

    /**
     * @param bool $isGuest
     *
     * @return $this
     */
    public function setIsGuest(bool $isGuest = self::NO): Profile
    {
        $this->is_guest = $isGuest;
        return $this;
    }
}