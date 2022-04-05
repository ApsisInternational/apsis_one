<?php

namespace Apsis\One\Module;

use Apsis\One\Api\Client;
use Apsis\One\Api\ClientFactory;
use Apsis\One\Context\LinkContext;
use Apsis\One\Helper\HelperInterface;
use Apsis_one;
use Throwable;
use Tools;
use HelperForm;
use AdminController;

class Configuration extends AbstractSetup
{
    /**
     * @param Apsis_one $module
     *
     * @return string
     */
    public function init(Apsis_one $module): string
    {
        $this->module = $module;
        $output = '';

        try {
            if (Tools::isSubmit('submit' . $this->module->name)) {
                $output .= $this->saveConfigurationValues() ?
                    $this->module->displayConfirmation('Settings updated.') :
                    $this->module->displayError('Unable to save some settings.');
            }

            return $output . $this->displayConfigurationForm();
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return $output;
        }
    }

    /**
     * @return bool
     */
    protected function saveConfigurationValues(): bool
    {
        try {
            $this->module->helper->logInfoMsg(__METHOD__);

            $profileSyncEnabled =  (int) Tools::getValue(self::CONFIG_KEY_PROFILE_SYNC_FLAG);
            $eventSyncEnabled =  (int) Tools::getValue(self::CONFIG_KEY_EVENT_SYNC_FLAG);
            $trackingScript = (string) Tools::getValue(self::CONFIG_KEY_TRACKING_CODE);
            return $this->configs->saveProfileSyncFlag($profileSyncEnabled) &&
                $this->configs->saveEventSyncFlag($eventSyncEnabled) &&
                $this->configs->saveTrackingCode($trackingScript);
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return false;
        }
    }

    /**
     * @return string
     */
    protected function displayConfigurationForm(): string
    {
        try {
            $helper = $this->initForm();
            if (empty($helper)) {
                return '';
            }

            /** @var LinkContext $context */
            $context = $this->module->helper->getService(HelperInterface::SERVICE_CONTEXT_LINK);

            // Load current value
            $helper->fields_value[self::READ_ONLY_FILED_BASE_URL] = $context->getBaseUrl();
            $helper->fields_value[self::CONFIG_KEY_GLOBAL_KEY] = $this->configs->getGlobalKey();

            $status = 'NOT CONNECTED';
            $installConfigs = $this->configs->getInstallationConfigs();
            if (! empty($installConfigs) &&
                ! empty($installConfigs[SetupInterface::INSTALLATION_CONFIG_SECTION_DISCRIMINATOR])
            ) {
                $status = $this->getSectionName(
                    $installConfigs[SetupInterface::INSTALLATION_CONFIG_SECTION_DISCRIMINATOR]
                );
            }

            $helper->fields_value[self::READ_ONLY_FIELD_ACCOUNT_STATUS] = $status;
            $helper->fields_value[self::CONFIG_KEY_PROFILE_SYNC_FLAG] =
                Tools::getValue(
                    self::CONFIG_KEY_PROFILE_SYNC_FLAG,
                    (int) $this->configs->getProfileSyncFlag()
                );
            $helper->fields_value[self::CONFIG_KEY_EVENT_SYNC_FLAG] =
                Tools::getValue(
                    self::CONFIG_KEY_EVENT_SYNC_FLAG,
                    (int) $this->configs->getEventSyncFlag()
                );
            $helper->fields_value[self::CONFIG_KEY_TRACKING_CODE] =
                Tools::getValue(
                    self::CONFIG_KEY_TRACKING_CODE,
                    $this->configs->getTrackingCode()
                );

            if (empty($formArray = $this->initFieldsFormArr())) {
                return '';
            }

            return $helper->generateForm($formArray);
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return '';
        }
    }

    /**
     * @param string $sectionDiscriminator
     *
     * @return string
     */
    protected function getSectionName(string $sectionDiscriminator): string
    {
        try {
            $status = 'NOT CONNECTED';

            /** @var ClientFactory $clientFactory */
            $clientFactory = $this->module->helper->getService(HelperInterface::SERVICE_MODULE_API_CLIENT_FACTORY);
            $client = $clientFactory->getApiClient();
            if (! $client instanceof Client) {
                return $status;
            }

            $response = $client->getSections();
            if (! isset($response->items) || ! is_array($response->items)) {
                return $status;
            }

            foreach ($response->items as $section) {
                if ($section->discriminator === $sectionDiscriminator) {
                    return sprintf('CONNECTED: SECTION > %s', strlen($section->name) ? $section->name : $section->id);
                }
            }

            return $status;
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return 'ERROR: Check log file.';
        }
    }

    /**
     * @return HelperForm|null
     */
    protected function initForm(): ?HelperForm
    {
        try {
            $helper = new HelperForm();

            // Get default language
            $defaultLang = (int) $this->configs->get('PS_LANG_DEFAULT');

            // Module, token and currentIndex
            $helper->module = $this->module;
            $helper->name_controller = $this->module->name;
            $helper->token = Tools::getAdminTokenLite('AdminModules');
            $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->module->name;

            // Language
            $helper->default_form_language = $defaultLang;
            $helper->allow_employee_form_lang = $defaultLang;

            // Title and toolbar
            $helper->title = $this->module->displayName;
            $helper->show_toolbar = true;
            $helper->toolbar_scroll = true;
            $helper->submit_action = 'submit' . $this->module->name;
            $helper->toolbar_btn = [
                'save' => [
                    'desc' => 'Save',
                    'href' => AdminController::$currentIndex . '&configure=' . $this->module->name . '&save' .
                        $this->module->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                ],
                'back' => [
                    'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                    'desc' => 'Back'
                ]
            ];
            return $helper;
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return null;
        }
    }

    /**
     * @return array
     */
    protected function initFieldsFormArr(): array
    {
        try {
            $fieldsForm[0]['form'] = [
                'legend' => [
                    'title' => 'Settings',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => 'Account Status',
                        'name' => self::READ_ONLY_FIELD_ACCOUNT_STATUS,
                        'readonly' => true,
                        'disabled' => true
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Base URL',
                        'name' => self::READ_ONLY_FILED_BASE_URL,
                        'readonly' => true,
                        'disabled' => true
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Shared Key',
                        'name' => self::CONFIG_KEY_GLOBAL_KEY,
                        'readonly' => true,
                        'disabled' => true
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Profile Sync Enabled',
                        'name' => self::CONFIG_KEY_PROFILE_SYNC_FLAG,
                        'required' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => self::FLAG_YES,
                                'label' => 'Yes',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => self::FLAG_NO,
                                'label' => 'No',
                            ]
                        ]
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Event Sync Enabled',
                        'name' => self::CONFIG_KEY_EVENT_SYNC_FLAG,
                        'required' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => self::FLAG_YES,
                                'label' => 'Yes',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => self::FLAG_NO,
                                'label' => 'No',
                            ]
                        ]
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'Tracking Script',
                        'desc' => 'If left empty feature is automatically disabled.',
                        'name' => self::CONFIG_KEY_TRACKING_CODE,
                    ],
                ],
                'submit' => [
                    'title' => 'Save',
                    'class' => 'btn btn-default pull-right'
                ]
            ];
            return $fieldsForm;
        } catch (Throwable $e) {
            $this->module->helper->logErrorMsg(__METHOD__, $e);
            return [];
        }
    }
}
