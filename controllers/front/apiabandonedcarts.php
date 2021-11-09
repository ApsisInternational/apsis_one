<?php

use Apsis\One\Controller\AbstractApiController;
use Apsis\One\Helper\DateHelper;
use Apsis\One\Helper\HelperInterface;
use Apsis\One\Model\Profile;
use Apsis\One\Helper\EntityHelper;

class apsis_OneApiabandonedcartsModuleFrontController extends AbstractApiController
{
    /**
     * {@inheritdoc}
     */
    protected function initClassProperties(): void
    {
        $this->validRequestMethod = self::VERB_GET;
        $this->validQueryParams = [
            self::QUERY_PARAM_CONTEXT_IDS => self::DATA_TYPE_STRING,
            self::QUERY_PARAM_AFTER_DATETIME => self::DATA_TYPE_INT,
            self::QUERY_PARAM_BEFORE_DATETIME => self::DATA_TYPE_INT
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function handleRequest(): void
    {
        try {
            $this->sendResponse();
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }

    /**
     * @return void
     */
    protected function sendResponse(): void
    {
        try {
            $this->exitWithResponse(
                $this->generateResponse(self::HTTP_CODE_200, $this->getAbandonedCartDataArr(), '', true)
            );
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }

    /**
     * @return array
     */
    protected function getAbandonedCartDataArr(): array
    {
        $items = [];

        try {
            /** @var DateHelper $dateHelper */
            $dateHelper = $this->module->helper->getService(HelperInterface::SERVICE_HELPER_DATE);

            $beforeDatetime = $dateHelper->convertDatetimeToShopsTimezoneAndFormat(
                '@' . $this->queryParams[self::QUERY_PARAM_BEFORE_DATETIME],
                (int) $this->groupId,
                (int) $this->shopId
            );
            $afterDatetime = $dateHelper->convertDatetimeToShopsTimezoneAndFormat(
                '@' . $this->queryParams[self::QUERY_PARAM_AFTER_DATETIME],
                (int) $this->groupId,
                (int) $this->shopId
            );

            /** @var EntityHelper $entityHelper */
            $entityHelper = $this->module->helper->getService(HelperInterface::SERVICE_HELPER_ENTITY);
            $abandonedCarts = $entityHelper->getAbandonedCartRepository()
                ->findForGivenShopsFilterByDateTime(
                    $this->module->helper->getStoreIdArrFromContext((int) $this->groupId, (int) $this->shopId),
                    $beforeDatetime,
                    $afterDatetime
                );

            if (is_array($abandonedCarts)) {
                foreach ($abandonedCarts as $abandonedCart) {
                    $dataArr = $entityHelper->getAbandonedCartDataArrForExport($abandonedCart);
                    /** @var Profile $profile */
                    $profile = $entityHelper->getProfileRepository()
                        ->findOneById($abandonedCart->getIdApsisProfile());

                    if (! empty($dataArr) && $profile instanceof Profile) {
                        $items[] = [
                            'profile_id' => $profile->getIdIntegration(),
                            'cart_data' => $dataArr
                        ];
                    }
                }
            }

        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }

        return [self::JSON_BODY_PARAM_ITEMS => $items];
    }
}
