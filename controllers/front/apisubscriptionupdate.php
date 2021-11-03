<?php

use Apsis\One\Controller\AbstractApiController;
use Apsis\One\Model\Profile;
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
        $this->validBodyParams = [self::BODY_PARAM_PROFILE_KEY => self::DATA_TYPE_STRING];
        $this->validQueryParams = [self::QUERY_PARAM_CONTEXT_IDS => self::DATA_TYPE_STRING];
    }

    /**
     * {@inheritdoc}
     */
    protected function handleRequest(): void
    {
        try {
            $this->validateProfileSyncFeature();

            if (empty($PK = $this->bodyParams[self::BODY_PARAM_PROFILE_KEY]) ||
                is_null($profile = $this->getProfile($PK))
            ) {
                $msg = 'Profile not found.';
                $this->module->helper->logDebugMsg(__METHOD__, ['info' => $msg]);

                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_404, [], $msg));
            }

            if ($this->updateSubscription($profile) === false) {
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
     * @param string $PK
     *
     * @return Profile|null
     */
    protected function getProfile(string $PK): ?Profile
    {
        try {
            if (strlen($PK)) {
                return $this->getProfileRepository()->findOneByIntegrationId($PK);
            }
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }

        return null;
    }

    /**
     * @param Profile $profile
     *
     * @return bool
     */
    protected function updateSubscription(Profile $profile): bool
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
}
