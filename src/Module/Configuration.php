<?php

namespace Apsis\One\Module;

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
            $profileSyncSize = (int) Tools::getValue(self::CONFIG_KEY_PROFILE_SYNC_SIZE, self::DEFAULT_SYNC_SIZE);
            $dbCleanUpAfter = (int) Tools::getValue(self::CONFIG_KEY_DB_CLEANUP_AFTER, self::DEFAULT_DB_CLEANUP_AFTER);
            return $this->configs->saveProfileSyncFlag($profileSyncEnabled) &&
                $this->configs->saveEventSyncFlag($eventSyncEnabled) &&
                $this->configs->saveTrackingCode($trackingScript) &&
                $this->configs->saveProfileSynSize($profileSyncSize) &&
                $this->configs->saveDbCleanUpAfter($dbCleanUpAfter);
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
            $helper->fields_value[self::CONFIG_KEY_PROFILE_SYNC_SIZE] = $this->configs->getProfileSynSize();
            $helper->fields_value[self::CONFIG_KEY_DB_CLEANUP_AFTER] = $this->configs->getDbCleanUpAfter();

            $helper->fields_value[self::READ_ONLY_FIELD_ACCOUNT_STATUS] =
                empty($this->configs->getInstallationConfigs()) ? 'NOT CONNECTED' : 'CONNECTED';
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
                        'type' => 'text',
                        'label' => 'Profile Sync Size',
                        'name' => self::CONFIG_KEY_PROFILE_SYNC_SIZE,
                        'required' => true
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
                    [
                        'type' => 'select',
                        'label' => 'DB Cleanup - After',
                        'name' => self::CONFIG_KEY_DB_CLEANUP_AFTER,
                        'required' => true,
                        'options' => [
                            'query' => [
                                ['id' => 7, 'name' => '7 Days'],
                                ['id' => 14, 'name' => '14 Days'],
                                ['id' => 30, 'name' => '30 Days'],
                                ['id' => 60, 'name' => '60 Days'],
                                ['id' => 90, 'name' => '90 Days'],
                                ['id' => 180, 'name' => '180 Days'],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'desc' => 'Cleanup cronjob will remove entries from DB tables older then set value.',
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
