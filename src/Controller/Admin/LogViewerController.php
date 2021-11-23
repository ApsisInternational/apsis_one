<?php

namespace Apsis\One\Controller\Admin;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;
use PrestaShopBundle\Security\Annotation\AdminSecurity;

class LogViewerController extends FrameworkBundleAdminController
{
    /**
     * @var string
     */
    protected $filename = '';

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = _PS_ROOT_DIR_ . '/var/logs/' . _PS_ENV_ . '_apsis.log';
    }


    /**
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))", message="Access denied.")
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        if (! file_exists($this->filename)) {
            $content = sprintf("The log file %s does not exist.", $this->filename);
        } elseif (! is_readable($this->filename)) {
            $content = sprintf("The log file %s is not readable.", $this->filename);
        } else {
            $size = filesize($this->filename);
            // Max read length 2000000 char = 2 MB and read file from end of file.
            $content = file_get_contents($this->filename, false, null, '-' . $size, 2000000);

        }

        return $this->render('@Modules/apsis_one/views/templates/admin/log_viewer.html.twig',
            ['header' => sprintf("File Path: %s", $this->filename), 'content' => $content]
        );
    }
}
