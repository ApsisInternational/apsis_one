<?php

namespace Apsis\One\Repository;

use Apsis\One\Helper\HelperInterface;
use Apsis\One\Module\Configuration\Configs;

abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var Configs
     */
    protected $configs;

    /**
     * @var HelperInterface
     */
    protected $helper;

    /**
     * ApiClientHelper constructor.
     *
     * @param Configs $configs
     * @param HelperInterface $helper
     */
    public function __construct(Configs $configs, HelperInterface $helper)
    {
        $this->configs = $configs;
        $this->helper = $helper;
    }
}