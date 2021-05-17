<?php

namespace Apsis\One\Api;

use Apsis\One\Helper\HelperInterface;
use Apsis\One\Module\Configuration\Configs;
use Apsis\One\Module\SetupInterface;
use Exception;

class ClientFactory
{
    /**
     * @var Configs
     */
    protected $configs;

    /**
     * @var HelperInterface
     */
    protected $helper;

    /**
     * ApiClientHelper constructor.
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
     * @return Client|null
     */
    public function getApiClientInstanceForContext(?int $idShopGroup = null, ?int $idShop = null): ?Client
    {
        try {
            $token = $this->helper->getToken($this, $idShopGroup, $idShop);
            if (empty($token)) {
                return null;
            }

            $host = $this->configs->getInstallationConfigByKey(
                SetupInterface::INSTALLATION_CONFIG_API_BASE_URL,
                $idShopGroup,
                $idShop
            );

            return empty($host) ? null : $this->getApiClientInstance($host, $token, true);
        } catch (Exception $e) {
            $this->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
            return null;
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
    public function getApiClientInstance(string $host, string $token = '', bool $isTokenNeeded = false): Client
    {
        return new Client($this->helper, $host, $token, $isTokenNeeded);
    }
}