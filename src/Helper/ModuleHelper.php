<?php

namespace Apsis\One\Helper;

use Apsis\One\Api\Client;
use Apsis\One\Api\ClientFactory;
use Apsis\One\Context\LinkContext;
use Apsis\One\Context\ShopContext;
use Apsis\One\Controller\ApiControllerInterface;
use Apsis\One\Model\EntityInterface;
use Apsis\One\Model\Profile;
use Apsis\One\Module\AbstractSetup;
use Apsis\One\Module\Configuration\Configs;
use Apsis\One\Module\SetupInterface as SI;
use Currency;
use Customer;
use PrestaShop\ModuleLibServiceContainer\DependencyInjection\ServiceContainer;
use PrestaShop\PrestaShop\Adapter\ContainerFinder;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Context;
use Module;
use Db;
use Throwable;
use Validate;

class ModuleHelper extends LoggerHelper
{
    /**
     * @return array
     */
    public function getAllAvailableHooks(): array
    {
        return array_merge(
            [self::CUSTOMER_HOOK_DISPLAY_ACCOUNT, self::DISPLAY_AFTER_BODY],
            self::GDPR_HOOKS,
            self::CUSTOMER_HOOKS,
            self::EMAIL_SUBSCRIPTION_HOOKS,
            self::ENTITY_ADDRESS_HOOKS,
            self::PRODUCT_COMMENT_HOOKS,
            self::WISHLIST_HOOKS,
            self::ORDER_HOOKS,
            self::CART_HOOKS
        );
    }

    /**
     * @return array
     */
    public function getAllHooksForProfileEntity(): array
    {
        return array_merge(
            $this->getAllCustomerHooks(),
            $this->getAllEmailSubscriptionHooks()
        );
    }

    /**
     * @return array
     */
    public function getAllCustomerHooks(): array
    {
        $customerHooks = self::CUSTOMER_HOOKS;
        return array_merge($customerHooks, self::ENTITY_ADDRESS_HOOKS);
    }

    /**
     * @return array
     */
    public function getAllEmailSubscriptionHooks(): array
    {
        return self::EMAIL_SUBSCRIPTION_HOOKS;
    }

    /**
     * @return array
     */
    public function getAllHooksForEventEntity(): array
    {
        return array_merge(
            self::PRODUCT_COMMENT_HOOKS,
            self::WISHLIST_HOOKS,
            self::ORDER_HOOKS,
            self::CART_HOOKS
        );
    }

    /**
     * @return bool
     */
    public function isModuleEnabledForCurrentShop(): bool
    {
        try {
            return (bool) Module::isEnabled(SI::MODULE_NAME);
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return bool
     */
    public function isModuleEnabledForContext(?int $idShopGroup = null, ?int $idShop = null): bool
    {
        $active = false;

        try {
            /** @var ShopContext $shopContext */
            $shopContext = $this->getService(self::SERVICE_CONTEXT_SHOP);

            if ($idShop) { // Need to check if enabled for the shop itself
                $shopList = [$idShop];
            } elseif ($idShopGroup) { // Need to check if enabled for minimum one shop under the group
                if (empty($list = $shopContext->getShopListGroupedByGroup()) || empty($list[$idShopGroup])) {
                    return $active;
                }
                $shopList = $list[$idShopGroup];
            } else { //Need to check if module is enabled for least one shop
                if (empty($shopList = $shopContext->getAllActiveShopIdsAsList())) {
                    return $active;
                }
            }

            $in = implode(',', array_map('intval', $shopList));
            $moduleId = Module::getModuleIdByName(SI::MODULE_NAME);
            $select = 'SELECT `id_module` FROM `' . AbstractSetup::getTableWithDbPrefix('module_shop') . '`';
            $where = 'WHERE `id_module` = ' . $moduleId .' AND `id_shop` IN (' . $in . ')';

            if (Db::getInstance()->getValue($select . ' ' . $where)) {
                $active = true;
            }
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }

        return $active;
    }

    /**
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return array
     */
    public function getStoreIdArrFromContext(int $idShopGroup, int $idShop): array
    {
        if ($idShop) {
            return [$idShop];
        }

        /** @var ShopContext $shopContext */
        $shopContext = $this->getService(self::SERVICE_CONTEXT_SHOP);

        if ($idShopGroup && ! empty($list = $shopContext->getShopListGroupedByGroup()) &&
            ! empty($list[$idShopGroup])
        ) {
            return $list[$idShopGroup];
        }

        if ($idShopGroup === 0 && $idShop === 0) {
            return $shopContext->getAllActiveShopIdsAsList();
        }

        return [];
    }

    /**
     * @param string $serviceName
     * @param string $container
     *
     * @return object|null
     */
    public function getService(string $serviceName, string $container = self::FROM_CONTAINER_MS): ?object
    {
        try {
            // First option
            if ($container === self::FROM_CONTAINER_MS) {
                return $this->getModuleSpecificContainer()->getService($serviceName);
            }

            // Second option
            if ($container === self::FROM_CONTAINER_FD) {
                return $this->getFromContainerFinderAdapter()->get($serviceName);
            }

            // Third option
            if ($container === self::FROM_CONTAINER_SA) {
                return $this->getFromSymfonyContainerAdapter()->get($serviceName);
            }
        } catch (Throwable $e) {
            // If true than all options are exhausted, log it
            if ($container === self::FROM_CONTAINER_SA) {
                $this->logDebugMsg(__METHOD__, ['info' => "All container options exhausted."]);
                $this->logErrorMsg(__METHOD__, $e);
                return null;
            }

            //Go through all options
            return $this->getService($serviceName, self::CONTAINER_RELATIONS[$container]);
        }

        return null;
    }

    /**
     * @return ServiceContainer
     */
    private function getModuleSpecificContainer(): ServiceContainer
    {
        return new ServiceContainer(SI::MODULE_NAME, _PS_MODULE_DIR_ . SI::MODULE_NAME . '/');
    }

    /**
     * @return ContainerInterface
     *
     * @throws Throwable
     */
    private function getFromContainerFinderAdapter(): ContainerInterface
    {
        return (new ContainerFinder(Context::getContext()))->getContainer();
    }

    /**
     * @return ContainerInterface
     */
    private function getFromSymfonyContainerAdapter(): ContainerInterface
    {
        return SymfonyContainer::getInstance();
    }

    /**
     * VALID RFC 4211 COMPLIANT Universally Unique Identifier (UUID) version 4
     * https://www.php.net/manual/en/function.uniqid.php#94959
     *
     * @return string
     */
    public static function generateUniversallyUniqueIdentifier(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * @param int $id
     *
     * @return Customer|null
     */
    public function getCustomerById(int $id): ?Customer
    {
        $customer = new Customer($id);
        if (Validate::isLoadedObject($customer)) {
            return $customer;
        }

        return null;
    }

    /**
     * @param int $idCurrency
     *
     * @return Currency|null
     */
    public function getCurrencyById(int $idCurrency): ?Currency
    {
        try {
            return new Currency($idCurrency);
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @param int $idCurrency
     *
     * @return string|null
     */
    public function getCurrencyIsoCodeById(int $idCurrency): ?string
    {
        try {
            if ($currency = $this->getCurrencyById($idCurrency)) {
                return $currency->iso_code;
            }

            return null;
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @param Profile $profile
     */
    public function mergePrestaShopProfileWithWebProfile(Profile $profile): void
    {
        /** @var Configs $configs */
        $configs = $this->getService(self::SERVICE_MODULE_CONFIGS);
        /** @var ClientFactory $clientFactory */
        $clientFactory = $this->getService(self::SERVICE_MODULE_API_CLIENT_FACTORY);

        $client = $clientFactory->getApiClient();
        $sectionDisc = $configs->getInstallationConfigByKey(SI::INSTALLATION_CONFIG_SECTION_DISCRIMINATOR);
        $keySpaceDisc = $configs->getInstallationConfigByKey(SI::INSTALLATION_CONFIG_KEYSPACE_DISCRIMINATOR);

        if (empty($sectionDisc) || empty($keySpaceDisc) || ! $client instanceof Client) {
            return;
        }

        $keySpacesToMerge = $this->getKeySpacesToMerge($profile->getIdIntegration(), $keySpaceDisc);
        if (empty($keySpacesToMerge)) {
            return;
        }

        if ($profile->getSyncStatus() !== EntityInterface::SS_SYNCED) {
            $emailAttributeVersionId = $this->getAttributeVersionId($client, $sectionDisc, self::EMAIL_DISC);
            if (empty($emailAttributeVersionId)) {
                return;
            }

            $status = $client->addAttributesToProfile(
                $keySpaceDisc,
                $profile->getIdIntegration(),
                $sectionDisc,
                [$emailAttributeVersionId => $profile->getEmail()]
            );

            if ($status !== null) {
                return;
            }
        }

        //If conflict on merge then set new cookie value for web keyspace
        if ($client->mergeProfile($keySpacesToMerge) === ApiControllerInterface::HTTP_CODE_409) {
            //Create new cookie value
            $keySpacesToMerge[1]['profile_key'] = md5($profile->getIdIntegration() . time());

            //Log it
            $this->logDebugMsg(
                __METHOD__,
                [
                    'Message' => 'Conflict merging with web profile, creating new cookie value.',
                    'Profile' => $profile->getIdIntegration(),
                    self::WEB_COOKIE_NAME => $keySpacesToMerge[1]['profile_key']
                ]
            );

            //Send second merge request
            if ($client->mergeProfile($keySpacesToMerge) === null) {
                $this->setNewCookieValue($keySpacesToMerge);
            }
        }
    }

    /**
     * @param Client $client
     * @param string $sectionDiscriminator
     * @param string $attrDisc
     *
     * @return int|null
     */
    public function getAttributeVersionId(Client $client, string $sectionDiscriminator, string $attrDisc): ?int
    {
        try {
            if (empty($sectionDiscriminator)) {
                return null;
            }

            $attributes = $client->getAttributes($sectionDiscriminator);
            if ($attributes && isset($attributes->items)) {
                foreach ($attributes->items as $attribute) {
                    if ($attribute->discriminator === $attrDisc) {
                        foreach ($attribute->versions as $version) {
                            if ($version->deprecated_at === null) {
                                return (int) $version->id;
                            }
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }

        return null;
    }

    /**
     * @param string $profileKey
     * @param string $keySpaceDiscriminator
     *
     * @return array|null
     */
    protected function getKeySpacesToMerge(string $profileKey, string $keySpaceDiscriminator): ?array
    {
        try {
            if (empty($keySpaceDiscriminator)) {
                return null;
            }

            $elyCookieValue = $_COOKIE[self::WEB_COOKIE_NAME] ?? null;
            if (empty($elyCookieValue)) {
                return null;
            }

            return [
                [
                    'keyspace_discriminator' => $keySpaceDiscriminator,
                    'profile_key' => $profileKey
                ],
                [
                    'keyspace_discriminator' => 'com.apsis1.keyspaces.web',
                    'profile_key' => $elyCookieValue
                ]
            ];
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }

        return null;
    }

    /**
     * @param array $keySpacesToMerge
     */
    protected function setNewCookieValue(array $keySpacesToMerge): void
    {
        try {
            $domain = $this->getDomainFromBaseUrl();
            if (is_string($domain) && strlen($domain)) {
                $status = setcookie(
                    self::WEB_COOKIE_NAME,
                    $keySpacesToMerge[1]['profile_key'],
                    self::WEB_COOKIE_DURATION + time(),
                    '/',
                    $domain
                );

                if (! $status) {
                    $this->logInfoMsg(
                        sprintf("%s. The cookie %s could not be sent.", __METHOD__, self::WEB_COOKIE_NAME)
                    );
                } else {
                    $info = ['Name' => self::WEB_COOKIE_NAME, 'Value' => $keySpacesToMerge[1]['profile_key']];
                    $this->logDebugMsg(__METHOD__, $info);
                }
            }
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @return string|null
     */
    protected function getDomainFromBaseUrl(): ?string
    {
        try {
            /** @var LinkContext $context */
            $context = $this->getService(self::SERVICE_CONTEXT_LINK);

            $baseUrl = $context->getBaseUrl();
            if (empty($baseUrl)) {
                return null;
            }

            $host = parse_url($baseUrl, PHP_URL_HOST);
            if (empty($host)) {
                return null;
            }

            $hostArr = explode('.', $host);
            if (empty($hostArr)) {
                return null;
            }

            if (count($hostArr) > 3) {
                return sprintf('.%s', $host);
            } else {
                $TLD = array_pop($hostArr);
                $SLD = array_pop($hostArr);
                return sprintf('.%s.%s', $SLD, $TLD);
            }
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
        }

        return null;
    }

    /**
     * @param Client $client
     * @param string $sectionDiscriminator
     *
     * @return array|null
     */
    public function getEventsDiscToVerMapping(Client $client, string $sectionDiscriminator): ?array
    {
        try {
            $eventDefinition = $client->getEvents($sectionDiscriminator);
            if (empty($eventDefinition) || ! isset($eventDefinition->items) || empty($eventDefinition->items)) {
                return null;
            }

            $versionsArr = [];
            foreach ($eventDefinition->items as $item) {
                foreach ($item->versions as $version) {
                    if ($version->deprecated_at === null) {
                        $versionsArr[$item->discriminator] = $version->id;
                        break;
                    }
                }
            }
            return $versionsArr;
        } catch (Throwable $e) {
            $this->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }
}
