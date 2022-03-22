<?php

namespace Apsis\One\Api;

use Apsis\One\Helper\HelperInterface;
use Apsis\One\Helper\DateHelper;
use Apsis\One\Module\Configuration\Configs;
use Apsis\One\Module\SetupInterface;
use Throwable;
use stdClass;

class ClientFactory
{
    /**
     * @var Configs
     */
    protected $configs;

    /**
     * @var DateHelper
     */
    protected $helper;

    /**
     * @var array
     */
    private $cachedClient = [];

    /**
     * ClientFactory constructor.
     *
     * @param Configs $configs
     * @param HelperInterface $helper
     */
    public function __construct(Configs $configs, HelperInterface $helper)
    {
        $this->configs = $configs;
        $this->helper = $helper;
    }

    /**
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return Client|null
     */
    public function getApiClient(?int $idShopGroup = null, ?int $idShop = null): ?Client
    {
        try {
            $configs = $this->configs->getClientCredentials($idShopGroup, $idShop);

            if (empty($configs)) {
                return null;
            }

            $isTokenExpired = $this->isTokenExpired($idShopGroup, $idShop);
            $clientFromCache = $this->getClientFromCache($configs, $idShopGroup, $idShop);

            if (! $isTokenExpired && $clientFromCache) {
                return $clientFromCache;
            }

            return $this->createInstance($configs, $idShopGroup, $idShop);

        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);

            return null;
        }
    }

    /**
     * @param array $configs
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return Client|null
     *
     * @throws Throwable
     */
    protected function createInstance(array $configs, ?int $idShopGroup = null, ?int $idShop = null): ?Client
    {

        $clientId = $configs[SetupInterface::INSTALLATION_CONFIG_CLIENT_ID];
        $clientSecret = $configs[SetupInterface::INSTALLATION_CONFIG_CLIENT_SECRET];
        $host = $configs[SetupInterface::INSTALLATION_CONFIG_API_BASE_URL];

        $apiClient = new Client($this->helper, $host);
        $apiClient->setConfigScope($this->configs, $idShopGroup, $idShop)
            ->setClientCredentials($clientId, $clientSecret);

        $token = $this->getToken($apiClient, $idShopGroup, $idShop);

        if (empty($token)) {
            return null;
        }

        $apiClient->setToken($token);
        return $this->cachedClient[$clientId] = $apiClient;
    }

    /**
     * @param array $configs
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return Client|null
     */
    protected function getClientFromCache(array $configs, ?int $idShopGroup = null, ?int $idShop = null): ?Client
    {
        $clientId = $configs[SetupInterface::INSTALLATION_CONFIG_CLIENT_ID];

        if (isset($this->cachedClient[$clientId])) {

            if ((bool) getenv('APSIS_DEVELOPER')) {
                $info = ['Client Id' => $clientId, 'idShopGroup' => $idShopGroup, 'idShop' => $idShop];
                $this->helper->logDebugMsg("apiClient from cache.", $info);
            }

            return $this->cachedClient[$clientId];
        }

        return null;
    }

    /**
     * @param Client $client
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return string
     */
    protected function getToken(Client $client, ?int $idShopGroup = null, ?int $idShop = null): string
    {
        $token = '';

        try{
            // Get from DB
            $token = $this->configs->getApiToken($idShopGroup, $idShop);
            if (strlen($token) && ! $this->isTokenExpired($idShopGroup, $idShop)) {
                return $token;
            }

            // Generate from API
            $response = $client->getAccessToken();

            //Success in generating token
            if ($response && isset($response->access_token)) {
                $info = ['idShopGroup' => $idShopGroup, 'idShop' => $idShop];
                $this->helper->logDebugMsg('Token renewed', $info);

                $this->saveTokenAndExpiry($response, $idShopGroup, $idShop);

                return (string) $response->access_token;
            }

            //Error in generating token, disable module & remove token along with token expiry
            if (isset($response->status) && in_array($response->status, Client::HTTP_CODES_DISABLE_MODULE)) {
                $this->helper->logDebugMsg(__METHOD__, (array) $response);
                $this->configs->disableSyncsClearTokenConfigs($idShopGroup, $idShop);
            }

        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
        }

        return $token;
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

        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);

            return true;
        }
    }

    /**
     * @param stdClass $request
     * @param int|null $idShopGroup
     * @param int|null $idShop
     */
    protected function saveTokenAndExpiry(stdClass $request, ?int $idShopGroup = null, ?int $idShop = null): void
    {
        try {
            $this->helper->logInfoMsg(__METHOD__);

            $time = $this->helper->getDateTimeFromTimeAndTimeZone()
                ->add($this->helper->getDateIntervalFromIntervalSpec(sprintf('PT%sS', $request->expires_in)))
                ->sub($this->helper->getDateIntervalFromIntervalSpec('PT60M'))
                ->format('Y-m-d H:i:s');

            $this->configs->saveApiToken($request->access_token, $idShopGroup, $idShop);
            $this->configs->saveApiTokenExpiry($time, $idShopGroup, $idShop);

        } catch (Throwable $e) {
            $this->helper->logErrorMsg(__METHOD__, $e);
        }
    }
}