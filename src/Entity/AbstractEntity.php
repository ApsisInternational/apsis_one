<?php

namespace Apsis\One\Entity;

use ObjectModel;
use PrestaShopDatabaseException;
use PrestaShopException;
use Context;

abstract class AbstractEntity extends ObjectModel implements EntityInterface
{
    /**
     * {@inheritdoc}
     */
    public $id;

    /**
     * @var int
     */
    public $id_apsis_profile;

    /**
     * {@inheritdoc}
     */
    public $id_shop;

    /**
     * @var int
     */
    public $sync_status = self::SS_PENDING;

    /**
     * @var string
     */
    public $error_message = self::EMPTY;

    /**
     * @var string
     */
    public $date_upd;

    /**
     * AbstractEntity constructor.
     *
     * @param null $id
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
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
        if (empty($this->getIdShop())) {
            $this->setIdShop(Context::getContext()->shop->id);
        }

        return parent::add($auto_date, $null_values);
    }

    /**
     * @param false $null_values
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($null_values = false): bool
    {
        return parent::update($null_values);
    }

    /**
     * @return bool
     *
     * @throws PrestaShopException
     */
    public function delete(): bool
    {
        return parent::delete();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id): AbstractEntity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdApsisProfile(): int
    {
        return (int) $this->id_apsis_profile;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setIdApsisProfile(int $id): AbstractEntity
    {
        $this->id_apsis_profile = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdShop(): int
    {
        return (int) $this->id_shop;
    }

    /**
     * @param int $idShop
     *
     * @return $this
     */
    public function setIdShop(int $idShop): AbstractEntity
    {
        $this->id_shop = $idShop;
        return $this;
    }

    /**
     * @return int
     */
    public function getSyncStatus(): int
    {
        return (int) $this->sync_status;
    }

    /**
     * @param int $syncStatus
     *
     * @return $this
     */
    public function setSyncStatus(int $syncStatus = self::SS_PENDING): AbstractEntity
    {
        $this->sync_status = $syncStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return (string) $this->error_message;
    }

    /**
     * @param string $errorMessage
     *
     * @return $this
     */
    public function setErrorMessage(string $errorMessage = self::EMPTY): AbstractEntity
    {
        $this->error_message = $errorMessage;
        return $this;
    }

    /**
     * @return string
     */
    public function getDateUpd(): string
    {
        return (string) $this->date_upd;
    }

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setDateUpd(string $date): AbstractEntity
    {
        $this->date_upd = $date;
        return $this;
    }

    /**
     * @return array
     *
     * @throws PrestaShopException
     */
    public function toArray(): array
    {
        return $this->getFields();
    }

    /**
     * @return string
     *
     * @throws PrestaShopException
     */
    public function toJson(): string
    {
        return (string) json_encode($this->toArray());
    }
}
