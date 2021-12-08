<?php

namespace Apsis\One\Form;

use AbstractForm;
use Apsis\One\Api\Client;
use Apsis\One\Api\ClientFactory;
use Apsis\One\Helper\HelperInterface as HI;
use Apsis\One\Helper\ModuleHelper;
use Apsis\One\Model\Profile;
use Apsis\One\Module\SetupInterface;
use FormField;
use Throwable;

class Consents extends AbstractForm
{
    const CHANNEL = 'com.apsis1.channels.email';

    /**
     * @var string
     */
    protected $template = 'module:apsis_one/views/templates/front/consents.tpl';

    /**
     * @var Profile
     */
    protected $profile;

    /**
     * @var ModuleHelper
     */
    protected $moduleHelper;

    /**
     * @var array
     */
    protected $insConfigs;

    /**
     * @var ClientFactory
     */
    protected $clientFactory;

    /**
     * @param Profile $profile
     * @param ModuleHelper $moduleHelper
     *
     * @return $this
     */
    public function setTopicsForFormatter(Profile $profile, ModuleHelper $moduleHelper): Consents
    {
        try {
            $this->profile = $profile;
            $this->moduleHelper = $moduleHelper;
            $this->clientFactory = $this->moduleHelper->getService(HI::SERVICE_MODULE_API_CLIENT_FACTORY);
            $configs = $this->moduleHelper->getService(HI::SERVICE_MODULE_CONFIGS);
            $this->insConfigs = $configs->getInstallationConfigs();

            $this->getFormatter()->setTopics($this->getTopicsToShow());
        } catch (Throwable $e) {
            $moduleHelper->logErrorMsg(__METHOD__, $e);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getTopicsToShow(): array
    {
        $formattedTopics = [];

        try {
            $client = $this->clientFactory->getApiClient();
            if (! $client instanceof Client) {
                return $formattedTopics;
            }

            $sectionDisc = $this->insConfigs[SetupInterface::INSTALLATION_CONFIG_SECTION_DISCRIMINATOR];
            $consentLists = $client->getConsentLists($sectionDisc);

            if (isset($consentLists->items) && ! empty($consentLists->items)) {
                foreach ($consentLists->items as $consentList) {
                    $topics = $client->getTopics($sectionDisc, $consentList->discriminator);
                    if (empty($topics->items)) {
                        continue;
                    }

                    $profileConsentsArr = $this->getSortedConsents($client, $sectionDisc, $consentList->discriminator);

                    foreach ($topics->items as $topic) {
                        if (! in_array($topic->discriminator, $profileConsentsArr)) {
                            continue;
                        }

                        $formattedTopics[] = [
                            'label' => $topic->name,
                            'name' => $topic->discriminator
                        ];
                    }
                }
            }
        } catch (Throwable $e) {
            $this->moduleHelper->logErrorMsg(__METHOD__, $e);
        }

        return $formattedTopics;
    }

    /**
     * @param Client $client
     * @param string $sectionDisc
     * @param string $cld
     *
     * @return array
     */
    protected function getSortedConsents(client $client, string $sectionDisc, string $cld): array
    {
        $sortedProfileConsents = [];

        try {
            $consent = $client->getOptInConsents(self::CHANNEL, $this->profile->getEmail(), $sectionDisc, $cld);
            if (empty($consent->items)) {
                return $sortedProfileConsents;
            }

            foreach ($consent->items as $consentTopic) {
                $sortedProfileConsents[] = $consentTopic->topic_discriminator;
            }
        } catch (Throwable $e) {
            $this->moduleHelper->logErrorMsg(__METHOD__, $e);
        }

        return $sortedProfileConsents;
    }

    /**
     * @return array
     */
    public function getTemplateVariables(): array
    {
        if (! $this->formFields) {
            $this->formFields = $this->formatter->getFormat();
        }

        return [
            'action' => $this->action,
            'formFields' => array_map(
                function (FormField $field) {
                    return $field->toArray();
                },
                $this->formFields
            ),
        ];
    }

    /**
     * @return bool
     */
    public function submit(): bool
    {
        return true;
    }
}