<?php

namespace Apsis\One\Module\Configuration;

use Apsis\One\Helper\HelperInterface;
use Apsis_one;
use Apsis\One\Context\ShopContext;
use Apsis\One\Module\SetupInterface;
use Configuration;
use PhpEncryption;
use Exception;
use PrestaShopException;

class Configs implements SetupInterface
{
    /**
     * @var PhpEncryption
     */
    protected $phpEncryption;

    /**
     * @var Apsis_one
     */
    protected $module;

    /**
     * @var ShopContext
     */
    protected $context;

    /**
     * Configs constructor.
     *
     * @param Apsis_one $module
     */
    public function __construct(Apsis_one $module)
    {
        $this->init($module);
    }

    /**
     * @param Apsis_one $module
     *
     * @return void
     */
    public function init(Apsis_one $module): void
    {
        $this->module = $module;
        $this->phpEncryption = new PhpEncryption(_NEW_COOKIE_KEY_);
        $this->context = $this->module->helper->getService(HelperInterface::SERVICE_CONTEXT_SHOP);
    }

    /**
     * @return bool
     */
    public function saveGlobalKey(): bool
    {
        try {
            $this->module->helper->logMsg(__METHOD__);

            return Configuration::updateGlobalValue(
                self::CONFIG_KEY_GLOBAL_KEY,
                $this->phpEncryption->encrypt($this->getRandomString(32))
            );
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return string
     */
    public function getGlobalKey(): string
    {
        try {
            return (string) $this->phpEncryption->decrypt(Configuration::getGlobalValue(self::CONFIG_KEY_GLOBAL_KEY));
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return '';
        }
    }

    /**
     * @return bool
     */
    public function deleteGlobalKey(): bool
    {
        try {
            $this->module->helper->logMsg(__METHOD__);

            return Configuration::deleteByName(self::CONFIG_KEY_GLOBAL_KEY);
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int $flag
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return bool
     */
    public function saveProfileSyncFlag(int $flag, ?int $idShopGroup = null, ?int $idShop = null): bool
    {
        try {
            $this->logValueChange(__METHOD__, $this->getProfileSyncFlag($idShopGroup, $idShop), $flag);

            return Configuration::updateValue(
                self::CONFIG_KEY_PROFILE_SYNC_ENABLED_FLAG,
                $flag,
                false,
                $idShopGroup,
                $idShop
            );
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return bool
     */
    public function getProfileSyncFlag(?int $idShopGroup = null, ?int $idShop = null): bool
    {
        try {
            return (boolean) Configuration::get(self::CONFIG_KEY_PROFILE_SYNC_ENABLED_FLAG, null, $idShopGroup, $idShop);
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteProfileSyncFlag(): bool
    {
        try {
            $this->logValueChange(__METHOD__, $this->getProfileSyncFlag());

            Configuration::deleteFromContext(self::CONFIG_KEY_PROFILE_SYNC_ENABLED_FLAG);
            return true;
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteProfileSyncFlagFromAllContext(): bool
    {
        try {
            $this->module->helper->logMsg(__METHOD__);

            return Configuration::deleteByName(self::CONFIG_KEY_PROFILE_SYNC_ENABLED_FLAG);
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int $flag
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return bool
     */
    public function saveEventSyncFlag(int $flag, ?int $idShopGroup = null, ?int $idShop = null): bool
    {
        try {
            $this->logValueChange(__METHOD__, $this->getEventSyncFlag($idShopGroup, $idShop), $flag);

            return Configuration::updateValue(
                self::CONFIG_KEY_EVENT_SYNC_ENABLED_FLAG,
                $flag,
                false,
                $idShopGroup,
                $idShop
            );
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return bool
     */
    public function getEventSyncFlag(?int $idShopGroup = null, ?int $idShop = null): bool
    {
        try {
            return (boolean) Configuration::get(self::CONFIG_KEY_EVENT_SYNC_ENABLED_FLAG, null, $idShopGroup, $idShop);
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteEventSyncFlag(): bool
    {
        try {
            $this->logValueChange(__METHOD__, $this->getEventSyncFlag());

            Configuration::deleteFromContext(self::CONFIG_KEY_EVENT_SYNC_ENABLED_FLAG);
            return true;
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteEventSyncFlagFromAllContext(): bool
    {
        try {
            $this->module->helper->logMsg(__METHOD__);

            return Configuration::deleteByName(self::CONFIG_KEY_EVENT_SYNC_ENABLED_FLAG);
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param string $jsCode
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return bool
     */
    public function saveTrackingCode(string $jsCode, ?int $idShopGroup = null, ?int $idShop = null): bool
    {
        try {
            $this->logValueChange(__METHOD__, $this->getTrackingCode($idShopGroup, $idShop), $jsCode);

            return Configuration::updateValue(
                self::CONFIG_KEY_TRACKING_CODE,
                $jsCode,
                true,
                $idShopGroup,
                $idShop
            );
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return string
     */
    public function getTrackingCode(?int $idShopGroup = null, ?int $idShop = null): string
    {
        try {
            return (string) Configuration::get(self::CONFIG_KEY_TRACKING_CODE, null, $idShopGroup, $idShop);
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return '';
        }
    }

    /**
     * @return bool
     */
    public function deleteTrackingCode(): bool
    {
        try {
            $this->logValueChange(__METHOD__, $this->getTrackingCode());

            Configuration::deleteFromContext(self::CONFIG_KEY_TRACKING_CODE);
            return true;
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteTrackingCodeFromAllContext(): bool
    {
        try {
            $this->module->helper->logMsg(__METHOD__);

            return Configuration::deleteByName(self::CONFIG_KEY_TRACKING_CODE);
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param array $configs
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return bool
     */
    public function saveInstallationConfigs(array $configs, ?int $idShopGroup = null, ?int $idShop = null): bool
    {
        try {
            $forLog = $configs;

            if (! empty($configs[self::INSTALLATION_CONFIG_CLIENT_SECRET])) {
                $configs[self::INSTALLATION_CONFIG_CLIENT_SECRET] =
                    $this->phpEncryption->encrypt($configs[self::INSTALLATION_CONFIG_CLIENT_SECRET]);

                $forLog[self::INSTALLATION_CONFIG_CLIENT_SECRET] = 'REMOVED FOR LOG';
            }

            if (($value = json_encode($configs)) === false) {
                return false;
            }


            $this->logValueChange(__METHOD__, $this->getInstallationConfigs($idShopGroup, $idShop), $forLog);

            return Configuration::updateValue(
                self::CONFIG_KEY_INSTALLATION_CONFIGS,
                empty($configs) ? '' : $value,
                false,
                $idShopGroup,
                $idShop
            );
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return array
     */
    public function getInstallationConfigs(?int $idShopGroup = null, ?int $idShop = null): array
    {
        try {
            $configs = (array) json_decode(
                Configuration::get(self::CONFIG_KEY_INSTALLATION_CONFIGS, null, $idShopGroup, $idShop)
            );

            if (! empty($configs[self::INSTALLATION_CONFIG_CLIENT_SECRET])) {
                $configs[self::INSTALLATION_CONFIG_CLIENT_SECRET] =
                    $this->phpEncryption->decrypt($configs[self::INSTALLATION_CONFIG_CLIENT_SECRET]);
            }

            return $configs;
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return [];
        }
    }

    /**
     * @param string $key
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return string
     */
    public function getInstallationConfigByKey(string $key, ?int $idShopGroup = null, ?int $idShop = null): string
    {
        try {
            $configs = $this->getInstallationConfigs($idShopGroup, $idShop);
            if (isset($configs[$key])) {
                return (string) $configs[$key];
            }

        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
        }
        return '';
    }

    /**
     * @return bool
     */
    public function deleteInstallationConfigs(): bool
    {
        try {
            $forLog = $this->getInstallationConfigs();
            if (! empty($forLog[self::INSTALLATION_CONFIG_CLIENT_SECRET])) {
                $forLog[self::INSTALLATION_CONFIG_CLIENT_SECRET] = 'REMOVED FOR LOG';
            }
            $this->logValueChange(__METHOD__, $forLog);

            Configuration::deleteFromContext(self::CONFIG_KEY_INSTALLATION_CONFIGS);
            return true;
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteInstallationConfigsFromAllContext(): bool
    {
        try {
            $this->module->helper->logMsg(__METHOD__);

            return Configuration::deleteByName(self::CONFIG_KEY_INSTALLATION_CONFIGS);
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param string $token
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return bool
     */
    public function saveApiToken(string $token, ?int $idShopGroup = null, ?int $idShop = null): bool
    {
        try {
            $this->module->helper->logMsg(__METHOD__);

            $context = $this->getContextForSavingConfig(self::CONFIG_KEY_INSTALLATION_CONFIGS, $idShopGroup, $idShop);
            return Configuration::updateValue(
                self::CONFIG_KEY_API_TOKEN,
                ($token) ? $this->phpEncryption->encrypt($token) : $token,
                false,
                $context['idShopGroup'],
                $context['idShop']
            );
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return string
     */
    public function getApiToken(?int $idShopGroup = null, ?int $idShop = null): string
    {
        try {
            $value = Configuration::get(self::CONFIG_KEY_API_TOKEN, null, $idShopGroup, $idShop);
            if ($value) {
                return $this->phpEncryption->decrypt($value);
            }
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
        }
        return '';
    }

    /**
     * @return bool
     */
    public function deleteApiToken(): bool
    {
        try {
            $this->module->helper->logMsg(__METHOD__);

            Configuration::deleteFromContext(self::CONFIG_KEY_API_TOKEN);
            return true;
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteApiTokenForAllContext(): bool
    {
        try {
            $this->module->helper->logMsg(__METHOD__);

            return Configuration::deleteByName(self::CONFIG_KEY_API_TOKEN);
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param string $tokenExpiry
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return bool
     */
    public function saveApiTokenExpiry(string $tokenExpiry, ?int $idShopGroup = null, ?int $idShop = null): bool
    {
        try {
            $this->logValueChange(__METHOD__, $this->getApiTokenExpiry($idShopGroup, $idShop), $tokenExpiry);

            $context = $this->getContextForSavingConfig(self::CONFIG_KEY_INSTALLATION_CONFIGS, $idShopGroup, $idShop);
            return Configuration::updateValue(
                self::CONFIG_KEY_API_TOKEN_EXPIRY,
                $tokenExpiry,
                false,
                $context['idShopGroup'],
                $context['idShop']
            );
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return string
     */
    public function getApiTokenExpiry(?int $idShopGroup = null, ?int $idShop = null): string
    {
        try {
            return (string) Configuration::get(self::CONFIG_KEY_API_TOKEN_EXPIRY, null, $idShopGroup, $idShop);
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return '';
        }
    }

    /**
     * @return bool
     */
    public function deleteApiTokenExpiry(): bool
    {
        try {
            $this->logValueChange(__METHOD__, $this->getApiTokenExpiry());

            Configuration::deleteFromContext(self::CONFIG_KEY_API_TOKEN_EXPIRY);
            return true;
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteApiTokenExpiryForAllContext(): bool
    {
        try {
            $this->module->helper->logMsg(__METHOD__);

            return Configuration::deleteByName(self::CONFIG_KEY_API_TOKEN_EXPIRY);
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int $size
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return bool
     */
    public function saveProfileSynSize(
        int $size = self::DEFAULT_PROFILE_SYNC_SIZE,
        ?int $idShopGroup = null,
        ?int $idShop = null
    ): bool
    {
        try {
            $this->logValueChange(__METHOD__, $this->getProfileSynSize($idShopGroup, $idShop), $size);

            return Configuration::updateValue(
                self::CONFIG_KEY_PROFILE_SYNC_SIZE,
                $size,
                true,
                $idShopGroup,
                $idShop
            );
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return int
     */
    public function getProfileSynSize(?int $idShopGroup = null, ?int $idShop = null): int
    {
        try {
            $value = (int) Configuration::get(self::CONFIG_KEY_PROFILE_SYNC_SIZE, null, $idShopGroup, $idShop);
            return ($value) ?: self::DEFAULT_PROFILE_SYNC_SIZE;
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return self::DEFAULT_PROFILE_SYNC_SIZE;
        }
    }

    /**
     * @return bool
     */
    public function deleteProfileSynSize(): bool
    {
        try {
            $this->logValueChange(__METHOD__, $this->getProfileSynSize());

            Configuration::deleteFromContext(self::CONFIG_KEY_PROFILE_SYNC_SIZE);
            return true;
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteProfileSynSizeForAllContext(): bool
    {
        try {
            $this->module->helper->logMsg(__METHOD__);

            return Configuration::deleteByName(self::CONFIG_KEY_PROFILE_SYNC_SIZE);
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int $size
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return bool
     */
    public function saveDbCleanUpAfter(
        int $size = self::DEFAULT_DB_CLEANUP_AFTER,
        ?int $idShopGroup = null,
        ?int $idShop = null
    ): bool
    {
        try {
            $this->logValueChange(__METHOD__, $this->getDbCleanUpAfter($idShopGroup, $idShop), $size);

            return Configuration::updateValue(
                self::CONFIG_KEY_DB_CLEANUP_AFTER,
                $size,
                true,
                $idShopGroup,
                $idShop
            );
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return int
     */
    public function getDbCleanUpAfter(?int $idShopGroup = null, ?int $idShop = null): int
    {
        try {
            $value = (int) Configuration::get(self::CONFIG_KEY_DB_CLEANUP_AFTER, null, $idShopGroup, $idShop);;
            return ($value) ?: self::DEFAULT_DB_CLEANUP_AFTER;
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return self::DEFAULT_DB_CLEANUP_AFTER;
        }
    }

    /**
     * @return bool
     */
    public function deleteDbCleanUpAfter(): bool
    {
        try {
            $this->logValueChange(__METHOD__, $this->getDbCleanUpAfter());

            Configuration::deleteFromContext(self::CONFIG_KEY_DB_CLEANUP_AFTER);
            return true;
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteDbCleanUpAfterForAllContext(): bool
    {
        try {
            $this->module->helper->logMsg(__METHOD__);

            return Configuration::deleteByName(self::CONFIG_KEY_DB_CLEANUP_AFTER);
        } catch (Exception $e) {
            $this->module->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int|null $idShopGroup
     * @param int|null $idShop
     * @param bool $installation
     *
     * @return bool
     *
     * @throws PrestaShopException
     */
    public function disableFeaturesAndDeleteConfig(
        ?int $idShopGroup = null,
        ?int $idShop = null,
        bool $installation = false
    ): bool
    {
        $this->module->helper->logMsg(__METHOD__);

        if ($idShopGroup || $idShop) {
            $this->context->setContext($idShopGroup, $idShop);
            if ($installation) {
                $this->deleteInstallationConfigs();
            }
            return $this->deleteApiToken() && $this->deleteApiTokenExpiry() && $this->deleteEventSyncFlag() &&
                $this->deleteProfileSyncFlag();
        } else {
            if ($installation) {
                $this->saveInstallationConfigs([], $idShopGroup, $idShop);
            }
            return $this->saveApiToken('', $idShopGroup, $idShop) &&
                $this->saveApiTokenExpiry('', $idShopGroup, $idShop) &&
                $this->saveEventSyncFlag(self::CONFIG_FLAG_NO, $idShopGroup, $idShop) &&
                $this->saveProfileSyncFlag(self::CONFIG_FLAG_NO, $idShopGroup, $idShop);
        }
    }

    /**
     * @param string $key
     * @param int|null $idLang
     * @param int|null $idShopGroup
     * @param int|null $idShop
     * @param boolean $default
     *
     * @return string
     */
    public function get(
        string $key,
        ?int $idLang = null,
        ?int $idShopGroup = null,
        ?int $idShop = null,
        bool $default = false
    ): string
    {
        return (string) Configuration::get($key, $idLang, $idShopGroup, $idShop, $default);
    }

    /**
     * @return int
     */
    public function getDefaultShopId(): int
    {
        return (int) $this->get('PS_SHOP_DEFAULT');
    }

    /**
     * @param string $key
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return array
     */
    public function getContextForSavingConfig(string $key, ?int $idShopGroup = null, ?int $idShop = null): array
    {
        if ($idShop && Configuration::hasKey($key, null, null, $idShop)) {
            return ['idShopGroup' => 0, 'idShop' => $idShop];
        } elseif ($idShopGroup && Configuration::hasKey($key, null, $idShopGroup)) {
            return ['idShopGroup' => $idShopGroup, 'idShop' => 0];
        } else {
            return ['idShopGroup' => 0, 'idShop' => 0];
        }
    }

    /**
     * @param int $length
     *
     * @return string
     */
    protected function getRandomString(int $length): string
    {
        $str = "";
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        $size = strlen( $chars );
        for( $i = 0; $i < $length; $i++ ) {
            $str .= $chars[ rand( 0, $size - 1 ) ];
        }

        return $str;
    }

    /**
     * @param string $method
     *
     * @param mixed $oldValue
     * @param mixed $newValue
     *
     * @return void
     */
    protected function logValueChange(string $method, $oldValue, $newValue = null): void
    {
        $this->module->helper->logMsg(__METHOD__);
        $this->module->helper->logMsg(['Method' => $method, 'Previous Value' => $oldValue, 'New Value' => $newValue]);
    }
}
