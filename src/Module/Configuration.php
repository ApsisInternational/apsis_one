<?php

namespace Apsis\One\Module;

use Apsis\One\Helper\LoggerHelper;
use Apsis\One\Repository\ConfigurationRepository;
use Apsis_one;
use Tools;
use HelperForm;
use AdminController;

class Configuration
{
    const READ_ONLY_FIELD_ACCOUNT_STATUS = 'APSIS_ONE_ACCOUNT_STATUS';
    const READ_ONLY_FILED_API_STORES_ENDPOINT = 'APSIS_ONE_API_STORES_ENDPOINT';
    const READ_ONLY_FILED_API_PROFILE_ENDPOINT = 'APSIS_ONE_API_PROFILE_ENDPOINT';
    const READ_ONLY_FILED_API_INSTALLATION_CONFIG_ENDPOINT = 'APSIS_ONE_API_INSTALLATION_CONFIG_ENDPOINT';
    const READ_ONLY_FILED_API_SUBSCRIPTION_UPDATE_ENDPOINT = 'APSIS_ONE_API_SUBSCRIPTION_UPDATE_ENDPOINT';

    const API_STORES_CONTROLLER_FILENAME = 'apistores';
    const API_PROFILE_CONTROLLER_FILENAME = 'apiprofiles';
    const API_INSTALLATION_CONFIG_CONTROLLER_FILENAME = 'apiinstallationconfig';
    const API_SUBSCRIPTION_UPDATE_CONTROLLER_FILENAME = 'apisubscriptionupdate';

    /**
     * @var ConfigurationRepository
     */
    protected $configurationRepository;

    /**
     * @var LoggerHelper
     */
    protected $loggerHelper;

    /**
     * @var Apsis_one
     */
    protected $module;

    /**
     * Install constructor.
     *
     * @param Apsis_one $module
     * @param ConfigurationRepository $configurationRepository
     * @param LoggerHelper $loggerHelper
     */
    public function __construct(
        Apsis_one $module,
        ConfigurationRepository $configurationRepository,
        LoggerHelper $loggerHelper
    ) {
        $this->module = $module;
        $this->configurationRepository = $configurationRepository;
        $this->loggerHelper = $loggerHelper;
    }

    /**
     * @return string
     */
    public function showConfigurations()
    {
        $output = '';

        if (Tools::isSubmit('submit' . $this->module->name)) {
            $profileSyncEnabled =  Tools::getValue(ConfigurationRepository::CONFIG_KEY_PROFILE_SYNC_ENABLED_FLAG);
            $eventSyncEnabled =  Tools::getValue(ConfigurationRepository::CONFIG_KEY_EVENT_SYNC_ENABLED_FLAG);
            $trackingScript = Tools::getValue(ConfigurationRepository::CONFIG_KEY_TRACKING_CODE);

            $output .= $this->saveConfigurationValues($profileSyncEnabled, $eventSyncEnabled, $trackingScript) ?
                $this->module->displayConfirmation($this->module->l('Settings updated.')) :
                $this->module->displayError($this->module->l('Unable to save some settings.'));
        }

        return $output . $this->displayConfigurationForm();
    }

    /**
     * @param int $profileSyncEnabled
     * @param int $eventSyncEnabled
     * @param string $trackingScript
     *
     * @return bool
     */
    private function saveConfigurationValues(int $profileSyncEnabled, int $eventSyncEnabled, string $trackingScript)
    {
        return $this->configurationRepository->saveProfileSyncFlag($profileSyncEnabled) &&
            $this->configurationRepository->saveEventSyncFlag($eventSyncEnabled) &&
            $this->configurationRepository->saveTrackingCode($trackingScript);
    }

    /**
     * @return string
     */
    private function displayConfigurationForm()
    {
        $helper = new HelperForm();
        $helper = $this->initForm($helper);

        // Load current value
        $helper->fields_value[self::READ_ONLY_FIELD_ACCOUNT_STATUS] =
            empty($this->configurationRepository->getInstallationConfigs()) ? 'NOT CONNECTED' : 'CONNECTED';
        $helper->fields_value[self::READ_ONLY_FILED_API_STORES_ENDPOINT] =
            $this->configurationRepository->getPrestaShopContext()->getLink()->getModuleLink(
                $this->module->name,
                self::API_STORES_CONTROLLER_FILENAME
            );
        $helper->fields_value[self::READ_ONLY_FILED_API_PROFILE_ENDPOINT] =
            $this->configurationRepository->getPrestaShopContext()->getLink()->getModuleLink(
                $this->module->name,
                self::API_PROFILE_CONTROLLER_FILENAME
            );
        $helper->fields_value[self::READ_ONLY_FILED_API_INSTALLATION_CONFIG_ENDPOINT] =
            $this->configurationRepository->getPrestaShopContext()->getLink()->getModuleLink(
                $this->module->name,
                self::API_INSTALLATION_CONFIG_CONTROLLER_FILENAME
            );
        $helper->fields_value[self::READ_ONLY_FILED_API_SUBSCRIPTION_UPDATE_ENDPOINT] =
            $this->configurationRepository->getPrestaShopContext()->getLink()->getModuleLink(
                $this->module->name,
                self::API_SUBSCRIPTION_UPDATE_CONTROLLER_FILENAME
            );
        $helper->fields_value[ConfigurationRepository::CONFIG_KEY_GLOBAL_KEY] =
            $this->configurationRepository->getGlobalKey();
        $helper->fields_value[ConfigurationRepository::CONFIG_KEY_PROFILE_SYNC_ENABLED_FLAG] =
            Tools::getValue(
                ConfigurationRepository::CONFIG_KEY_PROFILE_SYNC_ENABLED_FLAG,
                (int) $this->configurationRepository->getProfileSyncFlag()
            );
        $helper->fields_value[ConfigurationRepository::CONFIG_KEY_EVENT_SYNC_ENABLED_FLAG] =
            Tools::getValue(
                ConfigurationRepository::CONFIG_KEY_EVENT_SYNC_ENABLED_FLAG,
                (int) $this->configurationRepository->getEventSyncFlag()
            );
        $helper->fields_value[ConfigurationRepository::CONFIG_KEY_TRACKING_CODE] =
            Tools::getValue(
                ConfigurationRepository::CONFIG_KEY_TRACKING_CODE,
                $this->configurationRepository->getTrackingCode()
            );

        return $helper->generateForm($this->initFieldsFormArr());
    }

    /**
     * @param HelperForm $helper
     *
     * @return HelperForm
     */
    private function initForm(HelperForm $helper)
    {
        // Get default language
        $defaultLang = (int) $this->configurationRepository->get('PS_LANG_DEFAULT');

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
                'desc' => $this->module->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->module->name . '&save' .
                    $this->module->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->module->l('Back')
            ]
        ];
        return $helper;
    }

    /**
     * @return array
     */
    private function initFieldsFormArr()
    {
        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->module->l('Settings'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->module->l('Account Status'),
                    'name' => self::READ_ONLY_FIELD_ACCOUNT_STATUS,
                    'readonly' => true,
                    'disabled' => true
                ],
                [
                    'type' => 'text',
                    'label' => $this->module->l('Stores Endpoint [GET]'),
                    'name' => self::READ_ONLY_FILED_API_STORES_ENDPOINT,
                    'readonly' => true,
                    'disabled' => true
                ],
                [
                    'type' => 'text',
                    'label' => $this->module->l('Profiles Endpoint [GET]'),
                    'name' => self::READ_ONLY_FILED_API_PROFILE_ENDPOINT,
                    'readonly' => true,
                    'disabled' => true
                ],
                [
                    'type' => 'text',
                    'label' => $this->module->l('Installation Config Endpoint [POST]'),
                    'name' => self::READ_ONLY_FILED_API_INSTALLATION_CONFIG_ENDPOINT,
                    'readonly' => true,
                    'disabled' => true
                ],
                [
                    'type' => 'text',
                    'label' => $this->module->l('Subscription Update Endpoint [PATCH]'),
                    'name' => self::READ_ONLY_FILED_API_SUBSCRIPTION_UPDATE_ENDPOINT,
                    'readonly' => true,
                    'disabled' => true
                ],
                [
                    'type' => 'text',
                    'label' => $this->module->l('Shared Key'),
                    'name' => ConfigurationRepository::CONFIG_KEY_GLOBAL_KEY,
                    'readonly' => true,
                    'disabled' => true
                ],
                [
                    'type' => 'switch',
                    'label' => $this->module->l('Profile Sync Enabled'),
                    'name' => ConfigurationRepository::CONFIG_KEY_PROFILE_SYNC_ENABLED_FLAG,
                    'required' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => ConfigurationRepository::CONFIG_FLAG_YES,
                            'label' => $this->module->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => ConfigurationRepository::CONFIG_FLAG_NO,
                            'label' => $this->module->l('No'),
                        ]
                    ]
                ],
                [
                    'type' => 'switch',
                    'label' => $this->module->l('Event Sync Enabled'),
                    'name' => ConfigurationRepository::CONFIG_KEY_EVENT_SYNC_ENABLED_FLAG,
                    'required' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => ConfigurationRepository::CONFIG_FLAG_YES,
                            'label' => $this->module->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => ConfigurationRepository::CONFIG_FLAG_NO,
                            'label' => $this->module->l('No'),
                        ]
                    ]
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->module->l('Tracking Script'),
                    'desc' => $this->module->l('If left empty feature is automatically disabled.'),
                    'name' => ConfigurationRepository::CONFIG_KEY_TRACKING_CODE,
                ]
            ],
            'submit' => [
                'title' => $this->module->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        ];
        return $fieldsForm;
    }
}