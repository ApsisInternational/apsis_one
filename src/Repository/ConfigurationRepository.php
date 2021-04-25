<?php

namespace Apsis\One\Repository;

use Configuration;
use PhpEncryption;
use Exception;
use Apsis\One\Context\PrestaShopContext;
use Apsis\One\Helper\LoggerHelper;

class ConfigurationRepository
{
    const CONFIG_PREFIX = 'APSIS_ONE_';
    const CONFIG_KEY_GLOBAL_KEY = self::CONFIG_PREFIX . 'GLOBAL_KEY';
    const CONFIG_KEY_PROFILE_SYNC_ENABLED_FLAG = self::CONFIG_PREFIX . 'PROFILE_SYNC_ENABLED';
    const CONFIG_KEY_EVENT_SYNC_ENABLED_FLAG = self::CONFIG_PREFIX . 'EVENT_SYNC_ENABLED';
    const CONFIG_KEY_TRACKING_CODE = self::CONFIG_PREFIX . 'TRACKING_CODE';
    const CONFIG_KEY_INSTALLATION_CONFIGS = self::CONFIG_PREFIX . 'INSTALLATION_CONFIGS';
    const CONFIG_KEY_API_TOKEN = self::CONFIG_PREFIX . 'API_TOKEN';
    const CONFIG_KEY_API_TOKEN_EXPIRY = self::CONFIG_PREFIX . 'API_TOKEN_EXPIRY';

    const INSTALLATION_CONFIG_CLIENT_ID = 'client_id';
    const INSTALLATION_CONFIG_CLIENT_SECRET = 'client_secret';
    const INSTALLATION_CONFIG_ACCOUNT_ID = 'account_id';
    const INSTALLATION_CONFIG_SECTION_DISCRIMINATOR = 'section_discriminator';
    const INSTALLATION_CONFIG_KEYSPACE_DISCRIMINATOR = 'keyspace_discriminator';
    const INSTALLATION_CONFIG_API_BASE_URL = 'api_base_url';

    const CONFIG_FLAG_YES = 1;
    const CONFIG_FLAG_NO = 0;

    /**
     * @var PhpEncryption
     */
    private $phpEncryption;

    /**
     * @var LoggerHelper
     */
    private $loggerHelper;

    /**
     * @var PrestaShopContext
     */
    private $prestaShopContext;

    /**
     * ConfigurationRepository constructor.
     *
     * @param PrestaShopContext $prestashopContext
     * @param LoggerHelper $loggerHelper
     */
    public function __construct(PrestaShopContext $prestashopContext, LoggerHelper $loggerHelper)
    {
        $this->prestaShopContext = $prestashopContext;
        $this->phpEncryption = new PhpEncryption(_NEW_COOKIE_KEY_);
        $this->loggerHelper = $loggerHelper;
    }

    /**
     * @return bool
     */
    public function saveGlobalKey()
    {
        try {
            return Configuration::updateGlobalValue(
                self::CONFIG_KEY_GLOBAL_KEY,
                $this->phpEncryption->encrypt($this->getRandomString(32))
            );
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return string
     */
    public function getGlobalKey()
    {
        try {
            return (string) $this->phpEncryption->decrypt(Configuration::getGlobalValue(self::CONFIG_KEY_GLOBAL_KEY));
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return '';
        }
    }

    /**
     * @return bool
     */
    public function deleteGlobalKey()
    {
        try {
            return Configuration::deleteByName(self::CONFIG_KEY_GLOBAL_KEY);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int $flag
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return bool
     */
    public function saveProfileSyncFlag(int $flag, $idShopGroup = null, $idShop = null)
    {
        try {
            return Configuration::updateValue(
                self::CONFIG_KEY_PROFILE_SYNC_ENABLED_FLAG,
                $flag,
                false,
                $idShopGroup,
                $idShop
            );
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return bool
     */
    public function getProfileSyncFlag($idShopGroup = null, $idShop = null)
    {
        try {
            return (boolean) Configuration::get(self::CONFIG_KEY_PROFILE_SYNC_ENABLED_FLAG, null, $idShopGroup, $idShop);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteProfileSyncFlag()
    {
        try {
            Configuration::deleteFromContext(self::CONFIG_KEY_PROFILE_SYNC_ENABLED_FLAG);
            return true;
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteProfileSyncFlagFromAllContext()
    {
        try {
            return Configuration::deleteByName(self::CONFIG_KEY_PROFILE_SYNC_ENABLED_FLAG);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int $flag
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return bool
     */
    public function saveEventSyncFlag(int $flag, $idShopGroup = null, $idShop = null)
    {
        try {
            return Configuration::updateValue(
                self::CONFIG_KEY_EVENT_SYNC_ENABLED_FLAG,
                $flag,
                false,
                $idShopGroup,
                $idShop
            );
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return bool
     */
    public function getEventSyncFlag($idShopGroup = null, $idShop = null)
    {
        try {
            return (boolean) Configuration::get(self::CONFIG_KEY_EVENT_SYNC_ENABLED_FLAG, null, $idShopGroup, $idShop);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteEventSyncFlag()
    {
        try {
            Configuration::deleteFromContext(self::CONFIG_KEY_EVENT_SYNC_ENABLED_FLAG);
            return true;
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteEventSyncFlagFromAllContext()
    {
        try {
            return Configuration::deleteByName(self::CONFIG_KEY_EVENT_SYNC_ENABLED_FLAG);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param string $jsCode
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return bool
     */
    public function saveTrackingCode(string $jsCode, $idShopGroup = null, $idShop = null)
    {
        try {
            return Configuration::updateValue(
                self::CONFIG_KEY_TRACKING_CODE,
                $jsCode,
                true,
                $idShopGroup,
                $idShop
            );
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return string
     */
    public function getTrackingCode($idShopGroup = null, $idShop = null)
    {
        try {
            return (string) Configuration::get(self::CONFIG_KEY_TRACKING_CODE, null, $idShopGroup, $idShop);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return '';
        }
    }

    /**
     * @return bool
     */
    public function deleteTrackingCode()
    {
        try {
            Configuration::deleteFromContext(self::CONFIG_KEY_TRACKING_CODE);
            return true;
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteTrackingCodeFromAllContext()
    {
        try {
            return Configuration::deleteByName(self::CONFIG_KEY_TRACKING_CODE);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param array $configs
     * @param null $idShopGroup
     * @param null $idShop
     *
     * @return bool
     */
    public function saveInstallationConfigs(array $configs, $idShopGroup = null, $idShop = null)
    {
        try {
            if (! empty($configs[self::INSTALLATION_CONFIG_CLIENT_SECRET])) {
                $configs[self::INSTALLATION_CONFIG_CLIENT_SECRET] =
                    $this->phpEncryption->encrypt($configs[self::INSTALLATION_CONFIG_CLIENT_SECRET]);
            }

            if (($value = json_encode($configs)) === false) {
                return false;
            }

            return Configuration::updateValue(
                self::CONFIG_KEY_INSTALLATION_CONFIGS,
                empty($configs) ? '' : $value,
                false,
                $idShopGroup,
                $idShop
            );
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return array
     */
    public function getInstallationConfigs($idShopGroup = null, $idShop = null)
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
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return [];
        }
    }

    /**
     * @param string $key
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return string
     */
    public function getInstallationConfigByKey(string $key, $idShopGroup = null, $idShop = null)
    {
        try {
            $configs = $this->getInstallationConfigs($idShopGroup, $idShop);
            if (isset($configs[$key])) {
                return (string) $configs[$key];
            }

        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
        }
        return '';
    }

    /**
     * @return bool
     */
    public function deleteInstallationConfigs()
    {
        try {
            Configuration::deleteFromContext(self::CONFIG_KEY_INSTALLATION_CONFIGS);
            return true;
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteInstallationConfigsFromAllContext()
    {
        try {
            return Configuration::deleteByName(self::CONFIG_KEY_INSTALLATION_CONFIGS);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param string $token
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return bool
     */
    public function saveApiToken(string $token, $idShopGroup = null, $idShop = null)
    {
        try {
            $context = $this->getContextForSavingConfig(self::CONFIG_KEY_INSTALLATION_CONFIGS, $idShopGroup, $idShop);
            return Configuration::updateValue(
                self::CONFIG_KEY_API_TOKEN,
                ($token) ? $this->phpEncryption->encrypt($token) : $token,
                false,
                $context['idShopGroup'],
                $context['idShop']
            );
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return string
     */
    public function getApiToken($idShopGroup = null, $idShop = null)
    {
        try {
            $value = Configuration::get(self::CONFIG_KEY_API_TOKEN, null, $idShopGroup, $idShop);
            if ($value) {
                return $this->phpEncryption->decrypt($value);
            }
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
        }
        return '';
    }

    /**
     * @return bool
     */
    public function deleteApiToken()
    {
        try {
            Configuration::deleteFromContext(self::CONFIG_KEY_API_TOKEN);
            return true;
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteApiTokenForAllContext()
    {
        try {
            return Configuration::deleteByName(self::CONFIG_KEY_API_TOKEN);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param string $tokenExpiry
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return bool
     */
    public function saveApiTokenExpiry(string $tokenExpiry, $idShopGroup = null, $idShop = null)
    {
        try {
            $context = $this->getContextForSavingConfig(self::CONFIG_KEY_INSTALLATION_CONFIGS, $idShopGroup, $idShop);
            return Configuration::updateValue(
                self::CONFIG_KEY_API_TOKEN_EXPIRY,
                $tokenExpiry,
                false,
                $context['idShopGroup'],
                $context['idShop']
            );
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return string
     */
    public function getApiTokenExpiry($idShopGroup = null, $idShop = null)
    {
        try {
            return (string) Configuration::get(self::CONFIG_KEY_API_TOKEN_EXPIRY, null, $idShopGroup, $idShop);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return '';
        }
    }

    /**
     * @return bool
     */
    public function deleteApiTokenExpiry()
    {
        try {
            Configuration::deleteFromContext(self::CONFIG_KEY_API_TOKEN_EXPIRY);
            return true;
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function deleteApiTokenExpiryForAllContext()
    {
        try {
            return Configuration::deleteByName(self::CONFIG_KEY_API_TOKEN_EXPIRY);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return bool
     */
    public function disableFeaturesAndDeleteConfig($idShopGroup, $idShop)
    {
        $context = $this->getContextForSavingConfig(self::CONFIG_KEY_INSTALLATION_CONFIGS, $idShopGroup, $idShop);
        return $this->saveApiToken('', $context['idShopGroup'], $context['idShop']) &&
            $this->saveApiTokenExpiry('', $context['idShopGroup'], $context['idShop']) &&
            $this->saveEventSyncFlag(ConfigurationRepository::CONFIG_FLAG_NO, $idShopGroup, $idShop) &&
            $this->saveProfileSyncFlag(ConfigurationRepository::CONFIG_FLAG_NO, $idShopGroup, $idShop);
    }

    /**
     * @param string $key
     * @param null $idLang
     * @param null $idShopGroup
     * @param null $idShop
     * @param false $default
     *
     * @return false|string
     */
    public function get(string $key, $idLang = null, $idShopGroup = null, $idShop = null, $default = false)
    {
        return Configuration::get($key, $idLang, $idShopGroup, $idShop, $default);
    }

    /**
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return array
     */
    public function getContextForSavingConfig(string $key, $idShopGroup = null, $idShop = null)
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
    protected function getRandomString(int $length)
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
     * @return PrestaShopContext
     */
    public function getPrestaShopContext()
    {
        return $this->prestaShopContext;
    }
}
