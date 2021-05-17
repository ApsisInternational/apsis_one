<?php

namespace Apsis\One\Context;

use Link;
use Shop;

interface ContextInterface
{
    /**
     * @return Link|Shop
     */
    public function getContextObject();
}