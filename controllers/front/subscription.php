<?php

use Apsis\One\Form\Consents;
use Apsis\One\Form\ConsentsFormatter;
use Apsis\One\Helper\EntityHelper;
use Apsis\One\Helper\HelperInterface as HI;
use Apsis\One\Helper\ModuleHelper;
use Apsis\One\Module\SetupInterface;
use Apsis\One\Model\Profile;

class apsis_OneSubscriptionModuleFrontController extends ModuleFrontController
{
    /**
     * @inheritdoc
     */
    public $auth = true;

    /**
     * @inheritdoc
     */
    public $ssl = true;

    /**
     * @var ModuleHelper
     */
    protected $moduleHelper;

    public function __construct()
    {
        parent::__construct();
        $this->moduleHelper = new ModuleHelper();
    }

    /**
     * @inheritdoc
     */
    public function initContent()
    {
        try {
            /** @var EntityHelper $entityHelper */
            $entityHelper = $this->moduleHelper->getService(HI::SERVICE_HELPER_ENTITY);
            $profile = $entityHelper->getProfileRepository()->findOneByCustomerId($this->context->customer->id);
            if (! $profile instanceof Profile) {
                $this->errors[] = 'Unable to retrieve subscription information. Please try again later.';
                $this->redirectWithNotifications('index.php?controller=my-account');
            }

            $consents = (new Consents($this->context->smarty, $this->getTranslator(), new ConsentsFormatter()))
                ->setTopicsForFormatter($profile, $this->moduleHelper);

            $this->context->smarty->assign(
                [
                    'pageTitle' => 'Your Subscriptions',
                    'consentsForm' => $consents->getProxy(),
                    'formTemplate' => 'module:apsis_one/views/templates/front/consents.tpl'
                ]
            );

            parent::initContent();
            $this->setTemplate('module:apsis_one/views/templates/front/subscription.tpl');
        } catch (Throwable $e) {
            $this->moduleHelper->logErrorMsg(__METHOD__, $e);
            $this->errors[] = 'Something went wrong. We could not fetch your subscriptions. Please try again later.';
            $this->redirectWithNotifications('index.php?controller=my-account');
        }
    }

    /**
     * @return array
     */
    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();
        $breadcrumb['links'][] = [
            'title' => 'Subscriptions',
            'url' => $this->context->link->getModuleLink(SetupInterface::MODULE_NAME, 'subscription'),
        ];

        return $breadcrumb;
    }
}