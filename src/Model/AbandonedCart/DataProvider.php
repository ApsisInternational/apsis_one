<?php

namespace Apsis\One\Model\AbandonedCart;

use Apsis\One\Context\LinkContext;
use Apsis\One\Controller\ApiControllerInterface;
use Apsis\One\Helper\HelperInterface;
use Apsis\One\Model\Event\DataProvider as EventDataProvider;
use Apsis\One\Model\SchemaInterface;
use Apsis\One\Module\SetupInterface;

class DataProvider extends EventDataProvider
{
    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getTotalProductsInclTax(string $type): ?float
    {
        return $this->getFormattedValueByType('total_product_incl_tax', $type);
    }

    /**
     * @param string $type
     *
     * @return float|null
     */
    protected function getTotalProductsExclTax(string $type): ?float
    {
        return $this->getFormattedValueByType('total_product_excl_tax', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getCartToken(string $type): ?string
    {
        return $this->getFormattedValueByType('token', $type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    protected function getCartRebuildUrl(string $type): ?string
    {
        /** @var LinkContext $linkContext */
        $linkContext = $this->helper->getService(HelperInterface::SERVICE_CONTEXT_LINK);
        $this->objectData[SchemaInterface::SCHEMA_FIELD_CART_REBUILD_URL] = $linkContext->getModuleLink(
            SetupInterface::API_CART_REBUILD_CONTROLLER,
            [ApiControllerInterface::QUERY_PARAM_TOKEN => $this->getCartToken(SchemaInterface::DATA_TYPE_STRING)],
            true,
            null,
            $this->getShopId(SchemaInterface::DATA_TYPE_INT)
        );
        return $this->getFormattedValueByType(SchemaInterface::SCHEMA_FIELD_CART_REBUILD_URL, $type);
    }

    /**
     * @param string $type
     *
     * @return int|null
     */
    protected function getItemsCount(string $type): ?int
    {
        return $this->getFormattedValueByType('items_count', $type);
    }
}
