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
            self::QUERY_PARAM_AFTER_DATETIME => self::DATA_TYPE_STRING,
            self::QUERY_PARAM_BEFORE_DATETIME => self::DATA_TYPE_STRING
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

            $beforeDatetime = $dateHelper->convertDatetimeToStoreTimezoneAndFormat(
                $this->queryParams[self::QUERY_PARAM_BEFORE_DATETIME],
                $this->groupId,
                $this->shopId
            );
            $afterDatetime = $dateHelper->convertDatetimeToStoreTimezoneAndFormat(
                $this->queryParams[self::QUERY_PARAM_AFTER_DATETIME],
                $this->groupId,
                $this->shopId
            );

            if (! empty($beforeDatetime) && ! empty($afterDatetime)) {
                /** @var EntityHelper $entityHelper */
                $entityHelper = $this->module->helper->getService(HelperInterface::SERVICE_HELPER_ENTITY);
                $abandonedCarts = $entityHelper->getAbandonedCartRepository()
                    ->findForGivenShopsFilterByDateTime(
                        $this->module->helper->getStoreIdArrFromContext($this->groupId, $this->shopId),
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
                            $items[$abandonedCart->getId()] = [
                                'profile_id' => $profile->getIdIntegration(),
                                'cart_data' => $dataArr
                            ];
                        }
                    }
                }
            }

        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }

        return [self::JSON_BODY_PARAM_ITEMS => $items];
    }
}
