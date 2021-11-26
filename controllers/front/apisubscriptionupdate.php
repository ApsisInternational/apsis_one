<?php

use Apsis\One\Controller\AbstractApiController;
use Apsis\One\Model\Profile;
use Apsis\One\Model\SchemaInterface;
use Apsis\One\Module\HookProcessor;

class apsis_OneApisubscriptionupdateModuleFrontController extends AbstractApiController
{
    const REG_ARR = [Ps_Emailsubscription::GUEST_REGISTERED, Ps_Emailsubscription::CUSTOMER_REGISTERED];

    /**
     * {@inheritdoc}
     */
    protected function initClassProperties(): void
    {
        $this->validRequestMethod = self::VERB_PATCH;
        $this->validBodyParams = [
            self::BODY_PARAM_PROFILE_KEY => self::DATA_TYPE_STRING,
            self::BODY_PARAM_CONSENT_NAME => self::DATA_TYPE_STRING
        ];
        $this->validQueryParams = [self::QUERY_PARAM_CONTEXT_IDS => self::DATA_TYPE_STRING];
    }

    /**
     * {@inheritdoc}
     */
    protected function handleRequest(): void
    {
        try {
            $this->validateProfileSyncFeature();

            if (empty($profileKey = $this->bodyParams[self::BODY_PARAM_PROFILE_KEY]) ||
                empty($consentName = $this->bodyParams[self::BODY_PARAM_CONSENT_NAME]) ||
                is_null($profile = $this->getProfile($profileKey))
            ) {
                $msg = 'Profile not found.';
                $this->module->helper->logDebugMsg(__METHOD__, ['info' => $msg]);

                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_404, [], $msg));
            }

            if (isset($profile) && isset($consentName) && $this->updateSubscription($profile, $consentName) === false) {
                $msg = 'Unable to update subscription for Profile.';
                $this->module->helper->logDebugMsg(__METHOD__, ['info' => $msg]);

                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_500, [], $msg));
            }

            $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_204));
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }

    /**
     * @param string $profileKey
     *
     * @return Profile|null
     */
    protected function getProfile(string $profileKey): ?Profile
    {
        try {
            if (strlen($profileKey)) {
                return $this->getProfileRepository()->findOneByIntegrationId($profileKey);
            }
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }

        return null;
    }

    /**
     * @param Profile $profile
     * @param string $consentName
     *
     * @return bool
     */
    protected function updateSubscription(Profile $profile, string $consentName): bool
    {
        try {
            if ($consentName === SchemaInterface::SCHEMA_CONSENT_EMAIL_SUBSCRIPTION) {
                return $this->updateEmailSubscription($profile);
            } elseif ($consentName === SchemaInterface::SCHEMA_CONSENT_PARTNER_OFFERS) {
                return $this->updatePartnerOffersSubscription($profile);
            }
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }

        return false;
    }

    /**
     * @param Profile $profile
     *
     * @return bool
     */
    protected function updateEmailSubscription(Profile $profile): bool
    {
        try {
            $_POST['email'] = $profile->getEmail();
            $_POST['action'] = HookProcessor::ACT_UNSUB;
            $_POST[self::POST_KEY_UPDATE] = true;

            $subscription = new Ps_Emailsubscription;
            if (in_array($subscription->isNewsletterRegistered($_POST['email']), self::REG_ARR)) {
                $status = $subscription->newsletterRegistration();
                $this->module->helper->logDebugMsg(
                    __METHOD__,
                    ['Info' => $status, 'Profile' => $profile->id, 'Email' => $_POST['email']]
                );
            }

            return true;
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
            return false;
        }
    }

    /**
     * @param Profile $profile
     *
     * @return bool
     */
    protected function updatePartnerOffersSubscription(Profile $profile): bool
    {
        try {
            if (! empty($profile->getIdCustomer())) {
                $customer = $this->module->helper->getCustomerById($profile->getIdCustomer());
                if ($customer instanceof Customer && true === (bool) $customer->optin) {
                    $customer->optin = false;
                    return $customer->update();
                }
            }
            return true;
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
            return false;
        }
    }
}
