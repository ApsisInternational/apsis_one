<?php

namespace Apsis\One\Model;

use Apsis\One\Api\Client;
use Apsis\One\Api\ClientFactory;
use Apsis\One\Helper\EntityHelper;
use Apsis\One\Helper\HelperInterface as HI;
use Apsis\One\Helper\ModuleHelper;
use Apsis\One\Module\Configuration\Configs;
use Apsis\One\Module\SetupInterface;
use Db;
use ObjectModel;
use PrestaShopDatabaseException;
use PrestaShopException;
use Context;
use Validate;

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
    public $date_add;

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
        if ($this instanceof Profile && empty($this->getIdIntegration())) {
            $this->setIdIntegration(ModuleHelper::generateUniversallyUniqueIdentifier());
        }

        if ($this instanceof AbandonedCart && empty($this->getToken())) {
            $this->setToken(ModuleHelper::generateUniversallyUniqueIdentifier());
        }

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
        $this->setNecessaryFields();
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
        $this->setNecessaryFields();
        return parent::update($null_values);
    }

    /**
     * @return bool
     *
     * @throws PrestaShopException
     */
    public function delete(): bool
    {
        $check = parent::delete();

        // Only for Profile delete
        if ($check && $this instanceof Profile) {
            // Remove linked Events and ACs
            Db::getInstance()->delete(self::T_EVENT, self::C_ID_PROFILE . ' = ' . (int) $this->id);
            Db::getInstance()->delete(self::T_ABANDONED_CART, self::C_ID_PROFILE . ' = ' . (int) $this->id);

            // Remove from APSIS One
            $moduleHelper = new ModuleHelper();
            if (! $moduleHelper->isModuleEnabledForContext(null, $this->getIdShop())) {
                return true;
            }

            /** @var Configs $configs */
            $configs = $moduleHelper->getService(HI::SERVICE_MODULE_CONFIGS);
            if (empty($insConfigs = $configs->getInstallationConfigs(null, $this->getIdShop())) ||
                $configs->isAnyClientConfigMissing($insConfigs, null, $this->getIdShop())
            ) {
                return true;
            }

            /** @var ClientFactory $clientFactory */
            $clientFactory = $moduleHelper->getService(HI::SERVICE_MODULE_API_CLIENT_FACTORY);
            $client = $clientFactory->getApiClient(null, $this->getIdShop());
            if ($client instanceof Client) {
                $client->deleteProfile(
                    $insConfigs[SetupInterface::INSTALLATION_CONFIG_KEYSPACE_DISCRIMINATOR],
                    $this->getIdIntegration()
                );
            }
        }

        return $check;
    }

    /**
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function reset(): bool
    {
        if ($this instanceof AbandonedCart) {
            return true;
        }

        return $this->setSyncStatus()
            ->setErrorMessage()
            ->update();
    }

    /**
     * @param array $ids
     * @param string $class
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function resetSelection(array $ids, string $class): bool
    {
        $result = true;

        if ($this instanceof AbandonedCart) {
            return $result;
        }

        foreach ($ids as $id) {
            /** @var AbstractEntity $object */
            $object = new $class((int) $id);
            if (Validate::isLoadedObject($object)) {
                $result = $result && $object->reset();
            }
        }

        return $result;
    }

    /**
     * @param string $whereCond
     *
     * @return bool
     */
    public function resetProfilesAndEvents(string $whereCond): bool
    {
        if ($this instanceof AbandonedCart) {
            return true;
        }

        $columnData = [self::C_SYNC_STATUS => self::SS_JUSTIN];
        return Db::getInstance()->update(self::T_PROFILE, $columnData, $whereCond) &&
            Db::getInstance()->update(self::T_EVENT, $columnData, $whereCond);
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
    public function getDateAdd(): string
    {
        return (string) $this->date_add;
    }

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setDateAdd(string $date): Event
    {
        $this->date_add = $date;
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
     * @return $this
     */
    protected function setNecessaryFields(): AbstractEntity
    {
        // If no shop set
        if (empty($this->getIdShop())) {
            $this->setIdShop(Context::getContext()->shop->getContextShopID());
        }

        // Clears error field, in following conditions
        if (($this instanceof Event || $this instanceof Profile) && $this->getSyncStatus() === self::SS_PENDING) {
            $this->setErrorMessage(); // Clears field
        }

        // Set Profile Data field value
        if ($this instanceof Profile) {
            if ($this->getIdCustomer()) {
                $sql = sprintf(self::PROFILE_DATA_SQL_CUSTOMER, (int) $this->getIdCustomer());
            } elseif ($this->getIdNewsletter()) {
                $sql = sprintf(self::PROFILE_DATA_SQL_SUBSCRIBER, (int) $this->getIdNewsletter());
            }

            if (isset($sql)) {
                $this->setProfileData(EntityHelper::fetchSingleValueFromRow($sql, 'string'));
            } else {
                $this->setProfileData('');
            }
        }

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

    /**
     * @inheritDoc
     */
    public static function getRepositoryClassName(): string
    {
        return '';
    }
}
