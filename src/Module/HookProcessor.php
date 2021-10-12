<?php

namespace Apsis\One\Module;

use Apsis\One\Model\Profile;
use Apsis\One\Repository\ProfileRepository;
use Apsis_one;
use Apsis\One\Helper\HelperInterface;
use Apsis\One\Helper\EntityHelper;
use Context;
use Customer;
use Throwable;

class HookProcessor extends AbstractSetup
{
    /**
     * @var EntityHelper|null
     */
    private $entityHelper = null;

    /**
     * @param Apsis_one $module
     *
     * @return void
     */
    public function init(Apsis_one $module): void
    {
        $this->module = $module;
        $this->entityHelper = $module->helper->getService(HelperInterface::SERVICE_HELPER_ENTITY);
    }

    /**
     * @param string $hookName
     * @param array $hookArgs
     *
     * @return bool
     */
    public function processHook(string $hookName, array $hookArgs): bool
    {
        try {
            $hookName = lcfirst(str_replace('hook', '', $hookName));
            $this->module->helper->logDebugMsg($hookName, $hookArgs);

            if (in_array($hookName, $this->module->helper->getAllHooksForProfileEntity())) {
                $this->processHookForProfileEntity($hookName, $hookArgs);
            } elseif (in_array($hookName, $this->module->helper->getAllHooksForEventEntity())) {
                $this->processHookForEventEntity($hookName, $hookArgs);
            } elseif ($hookName === HelperInterface::CUSTOMER_HOOK_DISPLAY_ACCOUNT) {
                $this->processCustomerDisplayAccountHook($hookArgs);
            }

            return true;
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @param string $hookName
     * @param array $hookArgs
     */
    private function processHookForProfileEntity(string $hookName, array $hookArgs): void
    {
        try {
            $this->module->helper->logInfoMsg(__METHOD__);
            $repository = $this->entityHelper->getProfileRepository();

            if (in_array($hookName, $this->module->helper->getAllCustomerHooks()) &&
                ! empty($customer = array_shift($hookArgs)) && is_object($customer)
            ) {
                if ($hookName === HelperInterface::CUSTOMER_HOOK_DELETE_AFTER) {
                    //@todo Handle delete operation here. Customer deleted from database.
                } else {
                    $this->processCustomerEntityHooks($repository, $customer);
                }
            } elseif (in_array($hookName, $this->module->helper->getAllEmailSubscriptionHooks()) &&
                isset($hookArgs['email']) && strlen($hookArgs['email']) && empty($hookArgs['error'])
            ) {
                $this->processEmailSubscriptionEntityHooks($repository, $hookArgs['email']);
            }
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param ProfileRepository $profileRepository
     * @param Customer $customer
     */
    private function processCustomerEntityHooks(ProfileRepository $profileRepository, Customer $customer): void
    {
        $profile = $profileRepository->findOneByEmailForGivenShop($customer->email, $customer->id_shop);
        if ($customer->deleted) {
            //@todo Handle delete operation here. Customer deleted but remained in database.
        }

        if ($profile instanceof Profile) {
            $this->entityHelper->updateProfileForCustomerEntity($profile, $customer);
        } else {
            $this->entityHelper->createProfileForCustomerEntity($customer);
        }
    }

    /**
     * @param ProfileRepository $profileRepository
     * @param string $email
     */
    private function processEmailSubscriptionEntityHooks(ProfileRepository $profileRepository, string $email): void
    {
        $shopId = Context::getContext()->shop->id;
        $profile = $profileRepository->findOneByEmailForGivenShop($email, $shopId);
        $emailSubscriptionId = $this->entityHelper->getEmailSubscriptionIdByEmailAndShop($email, $shopId);

        if (is_null($emailSubscriptionId)) {
            if ($customerId = $this->entityHelper->getCustomerIdByEmailAndShop($email, $shopId, true)) {
                $this->processCustomerEntityHooks($profileRepository, new Customer($customerId));
            }
        } elseif ($profile instanceof Profile) {
            $this->entityHelper->updateProfileForEmailSubscriptionEntity($profile, $emailSubscriptionId);
        } else {
            $this->entityHelper->createProfileForEmailSubscriptionEntity($email, $emailSubscriptionId);
        }
    }

    /**
     * @param string $hookName
     * @param array $hookArgs
     */
    private function processHookForEventEntity(string $hookName, array $hookArgs): void
    {
        try {
            $this->module->helper->logInfoMsg(__METHOD__);
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param array $hookArgs
     */
    private function processCustomerDisplayAccountHook(array $hookArgs): void
    {
        try {
            $this->module->helper->logInfoMsg(__METHOD__);
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
        }
    }
}
