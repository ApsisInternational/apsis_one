<?php

namespace Apsis\One\Module\Configuration;

use Apsis_one;
use Apsis\One\Module\SetupInterface;
use Configuration;
use PhpEncryption;
use Exception;

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
    }

    /**
     * @return bool
     */
    public function saveGlobalKey(): bool
    {
        try {
            $this->module->helper->logInfoMsg(__METHOD__);

            return Configuration::updateGlobalValue(
                self::CONFIG_KEY_GLOBAL_KEY,
                $this->phpEncryption->encrypt($this->getRandomString(32))
            );
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return '';
        }
    }

    /**
     * @return bool
     */
    public function deleteGlobalKey(): bool
    {
        try {
            $this->module->helper->logInfoMsg(__METHOD__);

            return Configuration::deleteByName(self::CONFIG_KEY_GLOBAL_KEY);
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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
            $this->logValueChange(__METHOD__, $this->getProfileSyncFlag($idShopGroup, $idShop), (boolean) $flag);

            return Configuration::updateValue(
                self::CONFIG_KEY_PROFILE_SYNC_FLAG,
                $flag,
                false,
                $idShopGroup,
                $idShop
            );
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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
            return (boolean) Configuration::get(self::CONFIG_KEY_PROFILE_SYNC_FLAG, null, $idShopGroup, $idShop);
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteProfileSyncFlagFromAllContext(): bool
    {
        try {
            $this->module->helper->logInfoMsg(__METHOD__);

            return Configuration::deleteByName(self::CONFIG_KEY_PROFILE_SYNC_FLAG);
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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
            $this->logValueChange(__METHOD__, $this->getEventSyncFlag($idShopGroup, $idShop), (boolean) $flag);

            return Configuration::updateValue(
                self::CONFIG_KEY_EVENT_SYNC_FLAG,
                $flag,
                false,
                $idShopGroup,
                $idShop
            );
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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
            return (boolean) Configuration::get(self::CONFIG_KEY_EVENT_SYNC_FLAG, null, $idShopGroup, $idShop);
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteEventSyncFlagFromAllContext(): bool
    {
        try {
            $this->module->helper->logInfoMsg(__METHOD__);

            return Configuration::deleteByName(self::CONFIG_KEY_EVENT_SYNC_FLAG);
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return '';
        }
    }

    /**
     * @return bool
     */
    public function deleteTrackingCodeFromAllContext(): bool
    {
        try {
            $this->module->helper->logInfoMsg(__METHOD__);

            return Configuration::deleteByName(self::CONFIG_KEY_TRACKING_CODE);
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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

                $forLog[self::INSTALLATION_CONFIG_CLIENT_SECRET] = 'An encrypted value';
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
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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
            $this->module->helper->logErrorMsg(__METHOD__, $e);
        }
        return '';
    }

    /**
     * @return bool
     */
    public function deleteInstallationConfigsFromAllContext(): bool
    {
        try {
            $this->module->helper->logInfoMsg(__METHOD__);

            return Configuration::deleteByName(self::CONFIG_KEY_INSTALLATION_CONFIGS);
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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
            $this->module->helper->logInfoMsg(__METHOD__);

            $context = $this->getContextForUpdateConfig(self::CONFIG_KEY_INSTALLATION_CONFIGS, $idShopGroup, $idShop);
            return Configuration::updateValue(
                self::CONFIG_KEY_API_TOKEN,
                ($token) ? $this->phpEncryption->encrypt($token) : $token,
                false,
                $context['idShopGroup'],
                $context['idShop']
            );
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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
            $this->module->helper->logErrorMsg(__METHOD__, $e);
        }
        return '';
    }

    /**
     * @return bool
     */
    public function deleteApiTokenFromAllContext(): bool
    {
        try {
            $this->module->helper->logInfoMsg(__METHOD__);

            return Configuration::deleteByName(self::CONFIG_KEY_API_TOKEN);
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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

            $context = $this->getContextForUpdateConfig(self::CONFIG_KEY_INSTALLATION_CONFIGS, $idShopGroup, $idShop);
            return Configuration::updateValue(
                self::CONFIG_KEY_API_TOKEN_EXPIRY,
                $tokenExpiry,
                false,
                $context['idShopGroup'],
                $context['idShop']
            );
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return '';
        }
    }

    /**
     * @return bool
     */
    public function deleteApiTokenExpiryFromAllContext(): bool
    {
        try {
            $this->module->helper->logInfoMsg(__METHOD__);

            return Configuration::deleteByName(self::CONFIG_KEY_API_TOKEN_EXPIRY);
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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
    public function saveProfileSynSize(int $size, ?int $idShopGroup = null, ?int $idShop = null): bool
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
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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
            return ($value) ?: self::DEFAULT_SYNC_SIZE;
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return self::DEFAULT_SYNC_SIZE;
        }
    }

    /**
     * @return bool
     */
    public function deleteProfileSynSizeFromAllContext(): bool
    {
        try {
            $this->module->helper->logInfoMsg(__METHOD__);

            return Configuration::deleteByName(self::CONFIG_KEY_PROFILE_SYNC_SIZE);
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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
    public function saveDbCleanUpAfter(int $size, ?int $idShopGroup = null, ?int $idShop = null): bool
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
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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
            $value = (int) Configuration::get(self::CONFIG_KEY_DB_CLEANUP_AFTER, null, $idShopGroup, $idShop);
            return ($value) ?: self::DEFAULT_DB_CLEANUP_AFTER;
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return self::DEFAULT_DB_CLEANUP_AFTER;
        }
    }

    /**
     * @return bool
     */
    public function deleteDbCleanUpAfterFromAllContext(): bool
    {
        try {
            $this->module->helper->logInfoMsg(__METHOD__);

            return Configuration::deleteByName(self::CONFIG_KEY_DB_CLEANUP_AFTER);
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
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
    public function getContextForUpdateConfig(string $key, ?int $idShopGroup = null, ?int $idShop = null): array
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
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return bool
     */
    public function disableSyncsClearConfigs(?int $idShopGroup = null, ?int $idShop = null): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        return $this->disableSyncs($idShopGroup, $idShop) &&
            $this->clearTokenConfigs($idShopGroup, $idShop) &&
            $this->saveInstallationConfigs([], $idShopGroup, $idShop);
    }

    /**
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return bool
     */
    public function disableSyncsClearTokenConfigs(?int $idShopGroup = null, ?int $idShop = null): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        return $this->disableSyncs($idShopGroup, $idShop) &&
            $this->clearTokenConfigs($idShopGroup, $idShop);
    }

    /**
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return bool
     */
    public function clearTokenConfigs(?int $idShopGroup = null, ?int $idShop = null): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        return $this->saveApiToken('', $idShopGroup, $idShop) &&
            $this->saveApiTokenExpiry('', $idShopGroup, $idShop);
    }

    /**
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return bool
     */
    public function disableSyncs(?int $idShopGroup = null, ?int $idShop = null): bool
    {
        $this->module->helper->logInfoMsg(__METHOD__);

        return $this->saveEventSyncFlag(self::CONFIG_FLAG_NO, $idShopGroup, $idShop) &&
            $this->saveProfileSyncFlag(self::CONFIG_FLAG_NO, $idShopGroup, $idShop);
    }

    /**
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return array
     */
    public function getClientCredentials(?int $idShopGroup = null, ?int $idShop = null): array
    {
        try {
            $clientConfigs = [];
            foreach (SetupInterface::CLIENT_CONFIGS as $config) {
                $clientConfigs[$config] = $this->getInstallationConfigByKey($config, $idShopGroup, $idShop);
            }

            $isMissing = $this->isAnyClientConfigMissing($clientConfigs, $idShopGroup, $idShop);
            if ($isMissing) {
                $info = [
                    'Message' => 'Incomplete client credentials.',
                    'idShopGroup' => $idShopGroup,
                    'idShop' => $idShop
                ];
                $this->module->helper->logDebugMsg(__METHOD__, $info);

                $this->disableSyncs($idShopGroup, $idShop);

                return [];
            }

            return $clientConfigs;
        } catch (Exception $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return [];
        }
    }

    /**
     * @param array $configs
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return bool
     */
    protected function isAnyClientConfigMissing(array $configs, ?int $idShopGroup = null, ?int $idShop = null): bool
    {
        foreach (SetupInterface::CLIENT_CONFIGS as $config) {

            if (! isset($configs[$config]) || empty($configs[$config])) {

                $info = [
                    'Message' => 'Missing client credentials',
                    'idShopGroup' => $idShopGroup,
                    'idShop' => $idShop
                ];
                $this->module->helper->logDebugMsg(__METHOD__, $info);

                return true;
            }
        }

        return false;
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
     * @param mixed $oldValue
     * @param mixed $newValue
     *
     * @return void
     */
    protected function logValueChange(string $method, $oldValue, $newValue = null): void
    {
        $info = ['Method' => $method, 'Previous Value' => $oldValue, 'New Value' => $newValue];
        $this->module->helper->logDebugMsg(__METHOD__, $info);
    }
}
