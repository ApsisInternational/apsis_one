<?php

namespace Apsis\One\Model\Event;

use Apsis\One\Model\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * @param string $type
     *
     * @return int|null
     */
    protected function getWishlistId(string $type): ?int
    {
        return $this->getFormattedValueByType('id_wishlist', $type);
    }

    /**
     * @param string $type
     *
     * @return int|null
     */
    protected function getGuestId(string $type): ?int
    {
        return $this->getFormattedValueByType('id_guest', $type);
    }

    /**
     * @param string $type
     *
     * @return int|null
     */
    protected function getProductId(string $type): ?int
    {
        return $this->getFormattedValueByType('id_product', $type);
    }

    /**
     * @param string $type
     *
     * @return int|null
     */
    protected function getCartId(string $type): ?int
    {
        return $this->getFormattedValueByType('id_cart', $type);
    }

    /**
     * @param string $type
     *
     * @return int|null
     */
    protected function getCommentId(string $type): ?int
    {
        return $this->getFormattedValueByType('id_comment', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getReviewTitle(string $type): ?string
    {
        return $this->getFormattedValueByType('review_title', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getReviewDetail(string $type): ?string
    {
        return $this->getFormattedValueByType('review_detail', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getReviewAuthor(string $type): ?string
    {
        return $this->getFormattedValueByType('review_author', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getReviewRating(string $type): ?string
    {
        return $this->getFormattedValueByType('review_rating', $type);
    }

    /**
     * @param string $type
     *
     * @return int|null
     */
    protected function getOrderId(string $type): ?int
    {
        return $this->getFormattedValueByType('id_order', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getWishlistName(string $type): ?string
    {
        return $this->getFormattedValueByType('wishlist_name', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getProductName(string $type): ?string
    {
        return $this->getFormattedValueByType('product_name', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getProductReference(string $type): ?string
    {
        return $this->getFormattedValueByType('product_reference', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getProductImageUrl(string $type): ?string
    {
        return $this->getFormattedValueByType('product_image_url', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getProductUrl(string $type): ?string
    {
        return $this->getFormattedValueByType('product_url', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getCurrencyCode(string $type): ?string
    {
        return $this->getFormattedValueByType('currency_code', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getOrderReference(string $type): ?string
    {
        return $this->getFormattedValueByType('order_reference', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getPaymentMethod(string $type): ?string
    {
        return $this->getFormattedValueByType('payment_method', $type);
    }

    /**
     * @param string $type
     *
     * @return int|null
     */
    protected function getProductQty(string $type): ?int
    {
        return $this->getFormattedValueByType('product_qty', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getProductPriceAmountInclTax(string $type): ?float
    {
        return $this->getFormattedValueByType('product_price_amount_incl_tax', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getProductPriceAmountExclTax(string $type): ?float
    {
        return $this->getFormattedValueByType('product_price_amount_excl_tax', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getTotalDiscountsTaxIncl(string $type): ?float
    {
        return $this->getFormattedValueByType('total_discounts_tax_incl', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getTotalDiscountsTaxExcl(string $type): ?float
    {
        return $this->getFormattedValueByType('total_discounts_tax_excl', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getTotalPaidTaxIncl(string $type): ?float
    {
        return $this->getFormattedValueByType('total_paid_tax_incl', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getTotalPaidTaxExcl(string $type): ?float
    {
        return $this->getFormattedValueByType('total_paid_tax_excl', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getTotalWrappingTaxIncl(string $type): ?float
    {
        return $this->getFormattedValueByType('total_wrapping_tax_incl', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getTotalWrappingTaxExcl(string $type): ?float
    {
        return $this->getFormattedValueByType('total_wrapping_tax_excl', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getTotalProductsTaxIncl(string $type): ?float
    {
        return $this->getFormattedValueByType('total_products_tax_incl', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getTotalProductsTaxExcl(string $type): ?float
    {
        return $this->getFormattedValueByType('total_products_tax_excl', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getTotalShippingTaxIncl(string $type): ?float
    {
        return $this->getFormattedValueByType('total_shipping_tax_incl', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getTotalShippingTaxExcl(string $type): ?float
    {
        return $this->getFormattedValueByType('total_shipping_tax_excl', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getShippingTaxRate(string $type): ?float
    {
        return $this->getFormattedValueByType('shipping_tax_rate', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getUnitPriceTaxIncl(string $type): ?float
    {
        return $this->getFormattedValueByType('unit_price_tax_incl', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getUnitPriceTaxExcl(string $type): ?float
    {
        return $this->getFormattedValueByType('unit_price_tax_excl', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getTotalPriceTaxIncl(string $type): ?float
    {
        return $this->getFormattedValueByType('total_price_tax_incl', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getTotalPriceTaxExcl(string $type): ?float
    {
        return $this->getFormattedValueByType('total_price_tax_excl', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getTotalShippingPriceTaxIncl(string $type): ?float
    {
        return $this->getFormattedValueByType('total_shipping_price_tax_incl', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getTotalShippingPriceTaxExcl(string $type): ?float
    {
        return $this->getFormattedValueByType('total_shipping_price_tax_excl', $type);
    }

    /**
     * @param string $type
     *
     * @return bool|null
     */
    protected function getIsRecyclable(string $type): ?bool
    {
        return $this->getFormattedValueByType('is_recyclable', $type);
    }

    /**
     * @param string $type
     *
     * @return bool|null
     */
    protected function getIsGift(string $type): ?bool
    {
        return $this->getFormattedValueByType('is_gift', $type);
    }
}
