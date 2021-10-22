<?php

namespace Apsis\One\Module;

use Apsis\One\Model\EntityInterface as EI;
use Apsis\One\Helper\HelperInterface as HI;
use Apsis\One\Model\Profile;
use Apsis\One\Repository\ProfileRepository;
use Apsis_one;
use Apsis\One\Helper\EntityHelper;
use Context;
use Customer;
use Throwable;
use Validate;

class HookProcessor extends AbstractSetup
{
    /** NEWSLETTER SUBSCRIPTION ACTIONS */
    const ACT_SUBS = 0;
    const ACT_UNSUB = 1;

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
        $this->entityHelper = $module->helper->getService(HI::SERVICE_HELPER_ENTITY);
    }

    /**
     * @param string $hookName
     * @param array $hookArgs
     */
    public function processHook(string $hookName, array $hookArgs)
    {
        try {
            $hookName = lcfirst(str_replace('hook', '', $hookName));
            $this->module->helper->logDebugMsg($hookName, $hookArgs);

            if (in_array($hookName, $this->module->helper->getAllHooksForProfileEntity())) {
                $this->processHookForProfileEntity($hookName, $hookArgs);
            } elseif (in_array($hookName, $this->module->helper->getAllHooksForEventEntity())) {
                $this->processHookForEventEntity($hookName, $hookArgs);
            }
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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
                ! empty($object = array_shift($hookArgs)) && is_object($object)
            ) {
                if (! empty($object->id_customer)) {
                    $object = new Customer($object->id_customer);
                }

                if (Validate::isLoadedObject($object)) {
                    $this->processCustomerEntityHooks($repository, $object, $hookName);
                }
            } elseif (in_array($hookName, $this->module->helper->getAllEmailSubscriptionHooks()) &&
                ! empty($hookArgs['email']) && empty($hookArgs['error']) && isset($hookArgs['action'])
            ) {
                $this->processEmailSubscriptionEntityHooks($repository, $hookArgs['email'], $hookArgs['action'], $hookName);
            }
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param ProfileRepository $profileRepository
     * @param Customer $customer
     * @param string $hookName
     */
    private function processCustomerEntityHooks(
        ProfileRepository $profileRepository,
        Customer $customer,
        string $hookName
    ): void {
        try {
            $profile = $profileRepository->findOneByEmailForGivenShop($customer->email, $customer->id_shop);
            if ($customer->deleted || $hookName === HI::CUSTOMER_HOOK_DELETE_AFTER) {
                if ($profile instanceof Profile) {
                    $profile->delete();
                    // @todo remove Profile from One.
                }
            } elseif ($profile instanceof Profile) {
                $this->entityHelper->updateProfileForCustomerEntity($profile, $customer);
            } else {
                $profile = $this->entityHelper->createProfileForCustomerEntity($customer);
            }

            // Register Customer login event
            if ($profile instanceof Profile && $hookName === HI::CUSTOMER_HOOK_AUTH) {
                $this->entityHelper->registerEventForCustomer($customer, $profile->getId(), EI::ET_CUST_LOGIN);
            }
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
        }
    }

    /**
     * @param ProfileRepository $profileRepository
     * @param string $email
     * @param int $action
     * @param string $hookName
     */
    private function processEmailSubscriptionEntityHooks(
        ProfileRepository $profileRepository,
        string $email,
        int $action,
        string $hookName
    ): void {
        try {
            $shopId = Context::getContext()->shop->getContextShopID();
            $emailSubscriptionId = $this->entityHelper->getEmailSubscriptionIdByEmailAndShop($email, $shopId);
            $customerIdNotSubs = $this->entityHelper->getCustomerIdByEmailAndShop($email, $shopId, false);
            $customerIdIsSubs = $this->entityHelper->getCustomerIdByEmailAndShop($email, $shopId, true);
            $profile = $profileRepository->findOneByEmailForGivenShop($email, $shopId);

            // Subscribed
            if ($action === self::ACT_SUBS) {

                // New non Customer newsletter subscriber
                if ($emailSubscriptionId > 0 && ! $profile) {
                    $this->entityHelper->createProfileForEmailSubscriptionEntity($email, $emailSubscriptionId);

                }
                // Update to non customer Newsletter subscriber
                elseif ($emailSubscriptionId > 0 && $profile instanceof Profile) {
                    $this->entityHelper->updateProfileForEmailSubscriptionEntity($profile, $emailSubscriptionId);

                }
                // Customer subscribed to email newsletter
                elseif (! $emailSubscriptionId && $customerIdIsSubs > 0) {
                    $this->processCustomerEntityHooks($profileRepository, new Customer($customerIdIsSubs), $hookName);
                }

            // Unsubscribed
            } elseif ($action === self::ACT_UNSUB) {

                // Non Customer, record exist with active = 0. @todo Guest user could be unsubscribed using direct SQL.
                if ($emailSubscriptionId > 0 && $profile instanceof Profile) {
                    $this->entityHelper->updateProfileForEmailSubscriptionEntity($profile, $emailSubscriptionId, EI::NO);

                    // Event: Non customer user unsubscribes from newsletter
                    $this->entityHelper->registerSubsEventsForSubscriber(
                        $profile->getId(),
                        $profile->getIdNewsletter(),
                        EI::ET_NEWS_GUEST_OPTOUT
                    );
                }
                // Non Customer, record does not exist. Ps removed the record, we need to remove subscription from One.
                elseif (! $emailSubscriptionId && ! $customerIdIsSubs && $profile instanceof Profile) {

                    // Event: Non customer user unsubscribes from newsletter
                    $this->entityHelper->registerSubsEventsForSubscriber(
                        $profile->getId(),
                        $profile->getIdNewsletter(),
                        EI::ET_NEWS_GUEST_OPTOUT
                    );

                    $this->entityHelper->updateProfileForEmailSubscriptionEntity($profile, 0, false);

                    // @todo remove subscription from One in single call or figure our how to do with bulk.
                }
                // Is Customer, delegate to customer entity hook processor
                elseif (! $emailSubscriptionId && $customerIdNotSubs > 0) {
                    $this->processCustomerEntityHooks($profileRepository, new Customer($customerIdNotSubs), $hookName);
                }
            }
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
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

            if ($hookName === HI::CART_HOOK_UPDATE_QTY_BEFORE) {
                $this->entityHelper->registerProductCartedEvent($hookArgs);
            }

            if ($hookName === HI::PRODUCT_COMMENT_HOOK_VALIDATE) {
                $this->entityHelper->registerProductReviewEvent($hookArgs);
            }

            if ($hookName === HI::WISHLIST_HOOK_ADD_PRODUCT) {
                $this->entityHelper->registerProductWishedEvent($hookArgs);
            }
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
        }
    }
}
