<?php

namespace Apsis\One\Model\Event\Schema\Guest;

use Apsis\One\Model\Event\Schema\Subscriber\SubscriberIsGuest;

class GuestIsSubscriber extends SubscriberIsGuest
{
    /**
     * GuestIsSubscriber constructor.
     */
    public function __construct()
    {
        parent::__construct(self::EVENT_GUEST_IS_SUBSCRIBER_DISCRIMINATOR);
    }
}