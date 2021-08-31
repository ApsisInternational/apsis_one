<?php

namespace Apsis\One\Model\Profile;

use Apsis\One\Model\AbstractDataProvider;

// TODO: entity's values
class DataProvider extends AbstractDataProvider
{
    protected function getEntryId()
    {
       return $this->getProfileId();
    }

    protected function getProfileId()
    {
        //return $this->object->getSomeValue();
        return $this->object->profileId;
    }

    protected function getCustomerGroup()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getIsSubscribedToNewsletter()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getIsSubscribedToPartnerOffers()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getNewsletterDateAdded()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getIsActive()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getLanguageName()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getDateAdded()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getFirstName()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getLastName()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getAlias()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getEmail()
    {
        //return $this->object->getSomeValue();
        return $this->object->email;
    }

    protected function getGender()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getBirthday()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getCompany()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getBillingAddress1()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getBillingAddress2()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getBillingPostcode()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getBillingCity()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getBillingPhone()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getBillingPhoneMobile()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getBillingState()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getBillingCountry()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getShippingAddress1()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getShippingAddress2()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getShippingPostcode()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getShippingCity()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getShippingPhone()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getShippingPhoneMobile()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getShippingState()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getShippingCountry()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getLifetimeTotalSpend()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getAverageOrderValue()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getNewsletterSubscription()
    {
        //return $this->object->getSomeValue();
        return $this->getIsSubscribedToNewsletter();
    }
}
