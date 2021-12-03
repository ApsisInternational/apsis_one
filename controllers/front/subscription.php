<?php

use Apsis\One\Module\SetupInterface;

class apsis_OneSubscriptionModuleFrontController extends ModuleFrontController
{
    /**
     * @inheritdoc
     */
    public $auth = true;

    /**
     * @inheritdoc
     */
    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign(
            [
                'pageContent' => 'Subscriptions',
            ]
        );

        $this->setTemplate('module:apsis_one/views/templates/front/displayConsents.tpl');
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