<?php

namespace Apsis\One\Helper;

use Apsis\One\Module\Configuration\Configs;
use Apsis\One\Module\SetupInterface;
use Apsis\One\Api\ClientFactory;
use Exception;
use stdClass;

class ApiClientHelper extends LoggerHelper
{
    /**
     * @var Configs
     */
    private $configs;

    /**
     * @var DateHelper
     */
    private $helper;

    /**
     * ApiClientHelper constructor.
     *
     * @param Configs $configs
     * @param HelperInterface $helper
     */
    public function __construct(Configs $configs, HelperInterface $helper)
    {
        parent::__construct();
        $this->configs = $configs;
        $this->helper = $helper;
    }

    /**
     * @param ClientFactory $clientFactory
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return string
     */
    public function getToken(ClientFactory $clientFactory, ?int $idShopGroup = null, ?int $idShop = null): string
    {
        try{
            if(empty($configs = $this->configs->getInstallationConfigs($idShopGroup, $idShop)) ||
                empty($configs[SetupInterface::INSTALLATION_CONFIG_CLIENT_ID]) ||
                empty($configs[SetupInterface::INSTALLATION_CONFIG_CLIENT_SECRET]) ||
                empty($configs[SetupInterface::INSTALLATION_CONFIG_API_BASE_URL])
            ) {
                return '';
            }

            if ($this->isTokenExpired($idShopGroup, $idShop)) {
                return $this->getTokenFromApi($clientFactory, $configs, $idShopGroup, $idShop);
            } else {
                return ($token = $this->configs->getApiToken($idShopGroup, $idShop)) ? $token :
                    $this->getTokenFromApi($clientFactory, $configs, $idShopGroup, $idShop);
            }
        } catch (Exception $e) {
            $this->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return '';
        }
    }

    /**
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return bool
     */
    protected function isTokenExpired(?int $idShopGroup = null, ?int $idShop = null): bool
    {
        try {
            $expiryTime = $this->configs->getApiTokenExpiry($idShopGroup, $idShop);
            if (empty($expiryTime)) {
                return true;
            }
            $nowTime = $this->helper->getDateTimeFromTimeAndTimeZone()->format('Y-m-d H:i:s');
            return ($nowTime > $expiryTime);
        } catch (Exception $e) {
            $this->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return true;
        }
    }

    /**
     * @param ClientFactory $clientFactory
     * @param array $configs
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return string
     */
    protected function getTokenFromApi(
        ClientFactory $clientFactory,
        array $configs,
        ?int $idShopGroup = null,
        ?int $idShop = null
    ): string
    {
        try {
            $apiClient = $clientFactory->getApiClientInstance(
                $configs[SetupInterface::INSTALLATION_CONFIG_API_BASE_URL]
            );

            if (empty($apiClient)) {
                return '';
            }

            $response = $apiClient->getAccessToken(
                $configs[SetupInterface::INSTALLATION_CONFIG_CLIENT_ID],
                $configs[SetupInterface::INSTALLATION_CONFIG_CLIENT_SECRET]
            );

            if ($response && isset($response->access_token)) {
                return $this->saveTokenAndExpiry($response, $idShopGroup, $idShop) ?
                    (string) $response->access_token : '';
            }
            if ($response && isset($response->status) && in_array($response->status, [400, 401, 403])) {
                $this->configs->disableFeaturesAndDeleteConfig($idShopGroup, $idShop) ?
                    $this->logErrorMessage(__METHOD__, 'Disabled features, see api error') :
                    $this->logErrorMessage(__METHOD__, 'Unable to disable features');

            }
        } catch (Exception $e) {
            $this->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
        }
        return '';
    }

    /**
     * @param stdClass $request
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return bool
     */
    protected function saveTokenAndExpiry(stdClass $request, ?int $idShopGroup = null, ?int $idShop = null): bool
    {
        try {
            $this->logMsg(__METHOD__);

            $time = $this->helper->getDateTimeFromTimeAndTimeZone()
                ->add($this->helper->getDateIntervalFromIntervalSpec(sprintf('PT%sS', $request->expires_in)))
                ->format('Y-m-d H:i:s');

            return $this->configs->saveApiTokenExpiry($time, $idShopGroup, $idShop) &&
                $this->configs->saveApiToken($request->access_token, $idShopGroup, $idShop);
        } catch (Exception $e) {
            $this->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }
}