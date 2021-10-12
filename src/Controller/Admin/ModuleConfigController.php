<?php

namespace Apsis\One\Controller\Admin;

use Apsis\One\Module\SetupInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PrestaShopBundle\Security\Annotation\AdminSecurity;

class ModuleConfigController extends FrameworkBundleAdminController
{
    /**
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @return RedirectResponse
     */
    public function indexAction(): RedirectResponse
    {
        /** @var UrlGeneratorInterface $legacyUrlGenerator */
        $legacyUrlGenerator = $this->get('prestashop.core.admin.url_generator_legacy');
        return $this->redirect(
            $legacyUrlGenerator->generate('admin_module_configure_action', ['configure' => SetupInterface::MODULE_NAME])
        );
    }
}
