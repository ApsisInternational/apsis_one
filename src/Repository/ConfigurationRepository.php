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

    /**
     * @var int
     */
    private $shopId;

    /**
     * @var int
     */
    private $shopGroupId;

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
        $this->shopId = $prestashopContext->getCurrentShopId();
        $this->shopGroupId = $prestashopContext->getCurrentShopGroupId();
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
     *
     * @return bool
     */
    public function saveProfileSyncFlag(int $flag)
    {
        try {
            return Configuration::updateValue(self::CONFIG_KEY_PROFILE_SYNC_ENABLED_FLAG, $flag);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function getProfileSyncFlag()
    {
        try {
            return (boolean) Configuration::get(self::CONFIG_KEY_PROFILE_SYNC_ENABLED_FLAG);
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
     *
     * @return bool
     */
    public function saveEventSyncFlag(int $flag)
    {
        try {
            return Configuration::updateValue(self::CONFIG_KEY_EVENT_SYNC_ENABLED_FLAG, $flag);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return bool
     */
    public function getEventSyncFlag()
    {
        try {
            return (boolean) Configuration::get(self::CONFIG_KEY_EVENT_SYNC_ENABLED_FLAG);
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
     *
     * @return bool
     */
    public function saveTrackingCode(string $jsCode)
    {
        try {
            return Configuration::updateValue(self::CONFIG_KEY_TRACKING_CODE, $jsCode);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @return string
     */
    public function getTrackingCode()
    {
        try {
            return (string) Configuration::get(self::CONFIG_KEY_TRACKING_CODE);
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
            if (strlen($configs = (string) json_encode($configs)) === 0) {
                return false;
            }
            return Configuration::updateValue(
                self::CONFIG_KEY_INSTALLATION_CONFIGS,
                $configs,
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
     * @return array
     */
    public function getInstallationConfigs()
    {
        try {
            $value = (string) Configuration::get(self::CONFIG_KEY_INSTALLATION_CONFIGS);
            return empty($configs = (array) json_decode($value)) ? [] : $configs;
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return [];
        }
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
     *
     * @return bool
     */
    public function saveApiToken(string $token)
    {
        try {
            return Configuration::updateValue(self::CONFIG_KEY_API_TOKEN, $this->phpEncryption->encrypt($token));
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    public function getApiToken()
    {
        try {
            return (string) $this->phpEncryption->decrypt(Configuration::get(self::CONFIG_KEY_API_TOKEN));
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return '';
        }
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
