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
    const GLOBAL_KEY = self::CONFIG_PREFIX . 'GLOBAL_KEY';

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
     * ConfigurationRepository constructor.
     *
     * @param PrestaShopContext $prestashopContext
     * @param LoggerHelper $loggerHelper
     */
    public function __construct(PrestaShopContext $prestashopContext, LoggerHelper $loggerHelper)
    {
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
                self::GLOBAL_KEY,
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
            return (string) $this->phpEncryption->decrypt(Configuration::getGlobalValue(self::GLOBAL_KEY));
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
        return Configuration::deleteByName(self::GLOBAL_KEY);
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
}
