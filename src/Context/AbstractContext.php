<?php

namespace Apsis\One\Context;

use Apsis\One\Helper\HelperInterface;
use Context;
use Link;
use Shop;

abstract class AbstractContext implements ContextInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var HelperInterface
     */
    protected $helper;

    /**
     * @var Link|Shop
     */
    protected $contextObject;

    /**
     * PrestaShopContext constructor.
     */
    public function __construct(HelperInterface $helper)
    {
        $this->helper = $helper;
        $this->context = Context::getContext();
        $this->setContextObject();
    }

    /**
     * @return AbstractContext
     */
    abstract protected function setContextObject(): AbstractContext;

    /**
     * @inheritdoc
     */
    abstract public function getContextObject();
}