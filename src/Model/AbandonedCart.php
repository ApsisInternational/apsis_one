<?php

namespace Apsis\One\Model;

use Apsis\One\Repository\AbandonedCartRepository;

class AbandonedCart extends AbstractEntity
{
    /**
     * @var int
     */
    public $id_apsis_abandoned_cart;

    /**
     * @var int
     */
    public $id_cart;

    /**
     * @var string
     */
    public $cart_data;

    /**
     * @var string
     */
    public $token;

    /**
     * {@inheritdoc}
     */
    public static $definition = [
        'table' => self::T_ABANDONED_CART,
        'primary' => self::T_PRIMARY_MAPPINGS[self::T_ABANDONED_CART],
        'fields' => self::T_COLUMNS_MAPPINGS[self::T_ABANDONED_CART]
    ];

    /**
     * @inheritDoc
     */
    public static function getRepositoryClassName(): string
    {
        return AbandonedCartRepository::class;
    }

    /**
     * @return int
     */
    public function getIdApsisAbandonedCart(): int
    {
        return (int) $this->id_apsis_abandoned_cart;
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
        return (int) $this->id_cart;
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
        return (string) $this->cart_data;
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
        return (string) $this->token;
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
