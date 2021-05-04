<?php

namespace Apsis\One\Repository;

use Apsis\One\Api\Client;
use Apsis\One\Helper\LoggerHelper;
use Apsis\One\Helper\ApiClientHelper;
use Exception;

class ApiClientRepository
{
    /**
     * @var ConfigurationRepository
     */
    protected $configurationRepository;

    /**
     * @var LoggerHelper
     */
    protected $loggerHelper;

    /**
     * @var ApiClientHelper
     */
    protected $apiClientHelper;

    /**
     * ApiClientHelper constructor.
     *
     * @param ConfigurationRepository $configurationRepository
     * @param LoggerHelper $loggerHelper
     * @param ApiClientHelper $apiClientHelper
     */
    public function __construct(
        ConfigurationRepository $configurationRepository,
        LoggerHelper $loggerHelper,
        ApiClientHelper $apiClientHelper
    ) {
        $this->configurationRepository = $configurationRepository;
        $this->loggerHelper = $loggerHelper;
        $this->apiClientHelper = $apiClientHelper;
    }

    /**
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return Client|false
     */
    public function getApiClientInstanceForContext($idShopGroup = null, $idShop = null)
    {
        try {
            $token = $this->apiClientHelper->getToken($this, $idShopGroup, $idShop);
            if (empty($token)) {
                return false;
            }

            $host = $this->configurationRepository->getInstallationConfigByKey(
                ConfigurationRepository::INSTALLATION_CONFIG_API_BASE_URL,
                $idShopGroup,
                $idShop
            );

            return empty($host) ? false : $this->getApiClientInstance($host, $token, true);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param string $host
     * @param string $token
     * @param bool $isTokenNeeded
     *
     * @return Client
     *
     * @throws Exception
     */
    public function getApiClientInstance(string $host, string $token = '', bool $isTokenNeeded = false)
    {
        return new Client($this->loggerHelper, $host, $token, $isTokenNeeded);
    }
}