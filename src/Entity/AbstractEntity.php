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
    protected $id_apsis_profile;

    /**
     * @var int
     */
    protected $id_entity_ps;

    /**
     * @var int
     */
    protected $sync_status = self::SS_PENDING;

    /**
     * @var string
     */
    protected $error_message = self::EMPTY;

    /**
     * @var string
     */
    protected $date_upd;

    /**
     * {@inheritdoc}
     */
    protected $id_shop;

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
     * {@inheritdoc}
     */
    abstract public static function fetchCollectionClassName(): string;

    /**
     * {@inheritdoc}
     */
    abstract public static function fetchRepositoryClassName(): string;

    /**
     * {@inheritdoc}
     */
    public static function getRepositoryClassName(): string
    {
        return static::fetchRepositoryClassName();
    }

    /**
     * {@inheritdoc}
     */
    public static function getCollectionClassName(): string
    {
        return static::fetchCollectionClassName();
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
        return $this->id;
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
        return $this->id_apsis_profile;
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
        return $this->id_shop;
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
    public function getIdEntityPs(): int
    {
        return $this->id_entity_ps;
    }

    /**
     * @param int $idEntityPs
     *
     * @return $this
     */
    public function setIdEntityPs(int $idEntityPs): AbstractEntity
    {
        $this->id_entity_ps = $idEntityPs;
        return $this;
    }

    /**
     * @return int
     */
    public function getSyncStatus(): int
    {
        return $this->sync_status;
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
        return $this->error_message;
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
        return $this->date_upd;
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
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return (string) json_encode($this->toArray());
    }
}