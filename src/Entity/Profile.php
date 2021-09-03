<?php

namespace Apsis\One\Entity;

use Apsis\One\Helper\EntityHelper;
use PrestaShopDatabaseException;
use PrestaShopException;

class Profile extends AbstractEntity
{
    /**
     * @var string
     */
    public $id_integration;

    /**
     * @var string
     */
    public $email;

    /**
     * @var int
     */
    public $id_customer = self::NO_ID;

    /**
     * @var int
     */
    public $id_newsletter = self::NO_ID;

    /**
     * @var bool
     */
    public $is_customer = self::NO;

    /**
     * @var bool
     */
    public $is_guest = self::NO;

    /**
     * @var bool
     */
    public $is_newsletter = self::NO;

    /**
     * @var bool
     */
    public $is_offers = self::NO;

    /**
     * {@inheritdoc}
     */
    public static $definition = [
        'table' => self::T_PROFILE,
        'primary' => self::T_PRIMARY_MAPPINGS[self::T_PROFILE],
        'fields' => self::T_COLUMNS_MAPPINGS[self::T_PROFILE]
    ];

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
        return (string) $this->id_integration;
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
     * @return int
     */
    public function getIdCustomer(): int
    {
        return (int) $this->id_customer;
    }

    /**
     * @param int $idCustomer
     *
     * @return $this
     */
    public function setIdCustomer(int $idCustomer = self::NO_ID): Profile
    {
        $this->id_customer = $idCustomer;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdNewsletter(): int
    {
        return (int) $this->id_newsletter;
    }

    /**
     * @param int $idNewsletter
     *
     * @return $this
     */
    public function setIdNewsletter(int $idNewsletter = self::NO_ID): Profile
    {
        $this->id_newsletter = $idNewsletter;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return (string) $this->email;
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
        return (bool) $this->is_customer;
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
    public function getIsNewsletter(): bool
    {
        return (bool) $this->is_newsletter;
    }

    /**
     * @param bool $isNewsletter
     *
     * @return $this
     */
    public function setIsNewsletter(bool $isNewsletter = self::NO): Profile
    {
        $this->is_newsletter = $isNewsletter;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsOffers(): bool
    {
        return (bool) $this->is_offers;
    }

    /**
     * @param bool $isOffers
     *
     * @return $this
     */
    public function setIsOffers(bool $isOffers = self::NO): Profile
    {
        $this->is_offers = $isOffers;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsGuest(): bool
    {
        return (bool) $this->is_guest;
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
