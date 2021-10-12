<?php

namespace Apsis\One\Helper;

use Apsis\One\Context\ShopContext;
use Apsis\One\Module\AbstractSetup;
use Apsis\One\Module\SetupInterface;
use Context;
use Module;
use Db;
use PrestaShop\ModuleLibServiceContainer\DependencyInjection\ServiceContainer;
use PrestaShop\PrestaShop\Adapter\ContainerFinder;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Throwable;

class ModuleHelper extends LoggerHelper
{
    /**
     * @return array
     */
    public function getAllAvailableHooks(): array
    {
        return array_merge(
            self::CUSTOMER_HOOKS,
            self::EMAIL_SUBSCRIPTION_HOOKS,
            self::ENTITY_ADDRESS_HOOKS,
            self::ENTITY_GDPR_HOOKS,
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
        return [
            self::CUSTOMER_HOOK_ADD_AFTER,
            self::CUSTOMER_HOOK_UPDATE_AFTER,
            self::CUSTOMER_HOOK_DELETE_AFTER,
            self::EMAIL_SUBSCRIPTION_HOOK_REGISTER_AFTER
        ];
    }

    /**
     * @return array
     */
    public function getAllCustomerHooks(): array
    {
        return [
            self::CUSTOMER_HOOK_ADD_AFTER,
            self::CUSTOMER_HOOK_UPDATE_AFTER,
            self::CUSTOMER_HOOK_DELETE_AFTER
        ];
    }

    /**
     * @return array
     */
    public function getAllEmailSubscriptionHooks(): array
    {
        return [self::EMAIL_SUBSCRIPTION_HOOK_REGISTER_AFTER];
    }

    /**
     * @return array
     */
    public function getAllHooksForEventEntity(): array
    {
        return [
            self::CUSTOMER_HOOK_AUTH,
            self::PRODUCT_COMMENT_HOOK_VALIDATE,
            self::WISHLIST_HOOK_ADD_PRODUCT,
            self::ORDER_HOOK_VALIDATE,
            self::ORDER_HOOK_ADD_AFTER,
            self::CART_HOOK_UPDATE_QTY_BEFORE
        ];
    }

    /**
     * @return bool
     */
    public function isModuleEnabledForCurrentShop(): bool
    {
        try {
            return (bool) Module::isEnabled(SetupInterface::MODULE_NAME);
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
            $moduleId = Module::getModuleIdByName(SetupInterface::MODULE_NAME);
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
     * @param string $serviceName
     * @param string $container
     *
     * @return object|null
     */
    public function getService(string $serviceName, string $container = self::FROM_CONTAINER_MS)
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
        return new ServiceContainer(
            SetupInterface::MODULE_NAME,
            _PS_MODULE_DIR_ . SetupInterface::MODULE_NAME . '/'
        );
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
}
