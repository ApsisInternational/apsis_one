<?php

namespace Apsis\One\Model\Profile;

use Apsis\One\Model\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * @param string $type
     *
     * @return string
     */
    protected function getEntryId(string $type): string
    {
        return $this->getProfileId($type);
    }

    /**
     * @param string $type
     *
     * @return string
     */
    protected function getProfileId(string $type): string
    {
        return $this->getFormattedValueByType('id_integration', $type);
    }

    /**
     * @param string $type
     *
     * @return bool|null
     */
    protected function getEmailNewsletterSubscription(string $type): ?bool
    {
        return $this->getEmailNewsletter($type);
    }

    /**
     * @param string $type
     *
     * @return bool|null
     */
    protected function getPartnerOffersSubscription(string $type): ?bool
    {
        return $this->getPartnerOffers($type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getCustomerGroupName(string $type): ?string
    {
        return $this->getFormattedValueByType('default_group_name', $type);
    }

    /**
     * @param string $type
     *
     * @return bool|null
     */
    protected function getEmailNewsletter(string $type): ?bool
    {
        return $this->getFormattedValueByType('newsletter', $type);
    }

    /**
     * @param string $type
     *
     * @return bool|null
     */
    protected function getPartnerOffers(string $type): ?bool
    {
        return $this->getFormattedValueByType('optin', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getNewsletterDateAdded(string $type): ?string
    {
        return $this->getFormattedValueByType('newsletter_date_add', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getLanguageName(string $type): ?string
    {
        return $this->getFormattedValueByType('language_name', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getDateAdded(string $type): ?string
    {
        return $this->getFormattedValueByType('date_add', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getFirstName(string $type): ?string
    {
        return $this->getFormattedValueByType('firstname', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getLastName(string $type): ?string
    {
        return $this->getFormattedValueByType('lastname', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getEmail(string $type): ?string
    {
        return $this->getFormattedValueByType('email', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getGender(string $type): ?string
    {
        return $this->getFormattedValueByType('gender', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getBirthday(string $type): ?string
    {
        return $this->getFormattedValueByType('birthday', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getCompany(string $type): ?string
    {
        return $this->getFormattedValueByType('company', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getBillingAddress1(string $type): ?string
    {
        return $this->getFormattedValueByType('address1', $type, false, self::ADD_TYPE_BILLING);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getBillingAddress2(string $type): ?string
    {
        return $this->getFormattedValueByType('address2', $type, false, self::ADD_TYPE_BILLING);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getBillingPostcode(string $type): ?string
    {
        return $this->getFormattedValueByType('postcode', $type, false, self::ADD_TYPE_BILLING);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getBillingCity(string $type): ?string
    {
        return $this->getFormattedValueByType('city', $type, false, self::ADD_TYPE_BILLING);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getBillingState(string $type): ?string
    {
        return $this->getFormattedValueByType('state', $type, false, self::ADD_TYPE_BILLING);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getBillingCountry(string $type): ?string
    {
        return $this->getFormattedValueByType('country', $type, false, self::ADD_TYPE_BILLING);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getBillingPhone(string $type): ?string
    {
        return $this->getFormattedValueByType('phone', $type, true, self::ADD_TYPE_BILLING);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getBillingPhoneMobile(string $type): ?string
    {
        return $this->getFormattedValueByType('phone_mobile', $type, true, self::ADD_TYPE_BILLING);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getShippingAddress1(string $type): ?string
    {
        return $this->getFormattedValueByType('address1', $type, false, self::ADD_TYPE_SHIPPING);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getShippingAddress2(string $type): ?string
    {
        return $this->getFormattedValueByType('address2', $type, false, self::ADD_TYPE_SHIPPING);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getShippingPostcode(string $type): ?string
    {
        return $this->getFormattedValueByType('postcode', $type, false, self::ADD_TYPE_SHIPPING);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getShippingCity(string $type): ?string
    {
        return $this->getFormattedValueByType('city', $type, false, self::ADD_TYPE_SHIPPING);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getShippingState(string $type): ?string
    {
        return $this->getFormattedValueByType('state', $type, false, self::ADD_TYPE_SHIPPING);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getShippingCountry(string $type): ?string
    {
        return $this->getFormattedValueByType('country', $type, false, self::ADD_TYPE_SHIPPING);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getShippingPhone(string $type): ?string
    {
        return $this->getFormattedValueByType('phone', $type, true, self::ADD_TYPE_SHIPPING);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getShippingPhoneMobile(string $type): ?string
    {
        return $this->getFormattedValueByType('phone_mobile', $type, true, self::ADD_TYPE_SHIPPING);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getLifetimeTotalSpend(string $type): ?float
    {
        return $this->getFormattedValueByType('lifetime_total_spend', $type);
    }

    /**
     * @param string $type
     *
     * @return int|null
     */
    protected function getLifetimeTotalOrders(string $type): ?int
    {
        return $this->getFormattedValueByType('lifetime_total_orders', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getAverageOrderValue(string $type): ?float
    {
        return $this->getFormattedValueByType('average_order_value', $type);
    }
}
