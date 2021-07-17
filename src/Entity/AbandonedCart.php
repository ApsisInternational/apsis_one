<?php

namespace Apsis\One\Entity;

use Apsis\One\Entity\Repository\AbandonedCartRepository;
use Apsis\One\Entity\Collection\AbandonedCartCollection;
use Apsis\One\Helper\EntityHelper;
use PrestaShopDatabaseException;
use PrestaShopException;
use Shop;

class AbandonedCart extends AbstractEntity
{
    /**
     * @var int
     */
    protected $id_apsis_abandoned_cart;

    /**
     * @var int
     */
    protected $id_cart;

    /**
     * @var string
     */
    protected $cart_data;

    /**
     * @var string
     */
    protected $token;

    /**
     * {@inheritdoc}
     */
    public static $definition = [
        'table' => self::T_ABANDONED_CART,
        'primary' => self::T_PRIMARY_MAPPINGS[self::T_ABANDONED_CART],
        'fields' => self::T_COLUMNS_MAPPINGS[self::T_ABANDONED_CART],
        'associations' => [
            'shop' => [
                'type' => self::HAS_ONE,
                'field' => self::C_ID_SHOP,
                'object' => Shop::class,
                'association' => 'shop'
            ],
            'profile' => [
                'type' => self::HAS_ONE,
                'field' => self::C_ID_PROFILE,
                'object' => Profile::class,
                'association' => self::T_PROFILE
            ],
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public static function fetchCollectionClassName(): string
    {
        return AbandonedCartCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    public static function fetchRepositoryClassName(): string
    {
        return AbandonedCartRepository::class;
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
        if (empty($this->getToken())) {
            $this->setToken(EntityHelper::generateUniversallyUniqueIdentifier());
        }

        return parent::add($auto_date, $null_values);
    }

    /**
     * @return int
     */
    public function getIdApsisAbandonedCart(): int
    {
        return $this->id_apsis_abandoned_cart;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setIdApsisAbandonedCart(int $id): AbandonedCart
    {
        $this->id_apsis_abandoned_cart = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdCart(): int
    {
        return $this->id_cart;
    }

    /**
     * @param int $idCart
     *
     * @return $this
     */
    public function setIdCart(int $idCart): AbandonedCart
    {
        $this->id_cart = $idCart;
        return $this;
    }

    /**
     * @return string
     */
    public function getCartData(): string
    {
        return $this->cart_data;
    }

    /**
     * @param string $data
     *
     * @return $this
     */
    public function setCartData(string $data): AbandonedCart
    {
        $this->cart_data = $data;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $uuid
     *
     * @return $this
     */
    public function setToken(string $uuid): AbandonedCart
    {
        $this->token = $uuid;
        return $this;
    }
}