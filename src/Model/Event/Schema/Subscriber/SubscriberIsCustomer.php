<?php

namespace Apsis\One\Model\Event\Schema\Subscriber;

use Apsis\One\Model\Event\Schema\Customer\CustomerLogin;

class SubscriberIsCustomer extends CustomerLogin
{
    /**
     * SubscriberIsCustomer constructor.
     */
    public function __construct()
    {
        parent::__construct(self::EVENT_SUBSCRIBER_IS_CUSTOMER_DISCRIMINATOR);
    }
}