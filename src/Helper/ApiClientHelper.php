<?php

namespace Apsis\One\Helper;

use Apsis\One\Repository\ConfigurationRepository;
use Apsis\One\Repository\ApiClientRepository;
use Exception;
use stdClass;

class ApiClientHelper
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
     * @var DateHelper
     */
    protected $dateHelper;

    /**
     * ApiClientHelper constructor.
     *
     * @param ConfigurationRepository $configurationRepository
     * @param LoggerHelper $loggerHelper
     * @param DateHelper $dateHelper
     */
    public function __construct(
        ConfigurationRepository $configurationRepository,
        LoggerHelper $loggerHelper,
        DateHelper $dateHelper
    ) {
        $this->configurationRepository = $configurationRepository;
        $this->loggerHelper = $loggerHelper;
        $this->dateHelper = $dateHelper;
    }

    /**
     * @param ApiClientRepository $apiClientRepository
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return string
     */
    public function getToken(ApiClientRepository $apiClientRepository, $idShopGroup, $idShop)
    {
        if(empty($configs = $this->configurationRepository->getInstallationConfigs($idShopGroup, $idShop)) ||
            empty($configs[ConfigurationRepository::INSTALLATION_CONFIG_CLIENT_ID]) ||
            empty($configs[ConfigurationRepository::INSTALLATION_CONFIG_CLIENT_SECRET]) ||
            empty($configs[ConfigurationRepository::INSTALLATION_CONFIG_API_BASE_URL])
        ) {
            return false;
        }

        try{
            if ($this->isTokenExpired($idShopGroup, $idShop)) {
                return $this->getTokenFromApi($apiClientRepository, $configs, $idShopGroup, $idShop);
            } else {
                return ($token = $this->configurationRepository->getApiToken($idShopGroup, $idShop)) ? $token :
                    $this->getTokenFromApi($apiClientRepository, $configs, $idShopGroup, $idShop);
            }
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
    private function isTokenExpired($idShopGroup, $idShop)
    {
        try {
            $expiryTime = $this->configurationRepository->getApiTokenExpiry($idShopGroup, $idShop);
            if (empty($expiryTime)) {
                return true;
            }
            $nowTime = $this->dateHelper->getDateTimeFromTimeAndTimeZone()->format('Y-m-d H:i:s');
            return ($nowTime > $expiryTime);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return true;
        }
    }

    /**
     * @param ApiClientRepository $apiClientRepository
     * @param array $configs
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return string
     */
    private function getTokenFromApi(ApiClientRepository $apiClientRepository, array $configs, $idShopGroup, $idShop)
    {
        try {
            $apiClient = $apiClientRepository->getApiClientInstance(
                $configs[ConfigurationRepository::INSTALLATION_CONFIG_API_BASE_URL]
            );

            if (empty($apiClient)) {
                return '';
            }

            $response = $apiClient->getAccessToken(
                $configs[ConfigurationRepository::INSTALLATION_CONFIG_CLIENT_ID],
                $configs[ConfigurationRepository::INSTALLATION_CONFIG_CLIENT_SECRET]
            );

            if ($response && isset($response->access_token)) {
                return $this->saveTokenAndExpiry($idShopGroup, $idShop, $response) ?
                    (string) $response->access_token : '';
            }
            if ($response && isset($response->status) && in_array($response->status, [400, 401, 403])) {
                $this->configurationRepository->disableFeaturesAndDeleteConfig($idShopGroup, $idShop) ?
                    $this->loggerHelper->logErrorToFile(__METHOD__, 'Disabled features, see api error') :
                    $this->loggerHelper->logErrorToFile(__METHOD__, 'Unable to disable features');

            }
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
        }
        return '';
    }

    /**
     * @param $idShopGroup
     * @param $idShop
     * @param stdClass $request
     *
     * @return bool
     */
    private function saveTokenAndExpiry($idShopGroup, $idShop, stdClass $request)
    {
        try {
            $time = $this->dateHelper->getDateTimeFromTimeAndTimeZone()
                ->add($this->dateHelper->getDateIntervalFromIntervalSpec(sprintf('PT%sS', $request->expires_in)))
                ->format('Y-m-d H:i:s');

            return $this->configurationRepository->saveApiTokenExpiry($time, $idShopGroup, $idShop) &&
                $this->configurationRepository->saveApiToken($request->access_token, $idShopGroup, $idShop);
        } catch (Exception $e) {
            $this->loggerHelper->logErrorToFile(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }
}