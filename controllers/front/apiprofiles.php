<?php

use Apsis\One\Controller\AbstractApiController;
use Apsis\One\Helper\HelperInterface;
use Apsis\One\Model\Profile\Schema;
use Apsis\One\Module\SetupInterface;
use Apsis\One\Context\LinkContext;
use Apsis\One\Helper\EntityHelper;
use Apsis\One\Model\EntityInterface as EI;

class apsis_OneApiprofilesModuleFrontController extends AbstractApiController
{
    /**
     * {@inheritdoc}
     */
    protected function initClassProperties(): void
    {
        $this->validRequestMethod = self::VERB_GET;
        $this->validQueryParams = [self::QUERY_PARAM_CONTEXT_IDS => self::DATA_TYPE_STRING];
        $this->optionalQueryParams = [
            self::QUERY_PARAM_SCHEMA => self::DATA_TYPE_INT,
            self::QUERY_PARAM_AFTER_ID => self::DATA_TYPE_INT,
            self::QUERY_PARAM_INCLUDE_EVENTS => self::DATA_TYPE_INT
        ];
        $this->optionalQueryParamIgnoreRelations = [
            self::QUERY_PARAM_SCHEMA => [self::PARAM_TYPE_QUERY => [self::QUERY_PARAM_CONTEXT_IDS]]
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function handleRequest(): void
    {
        try {
            //Send schema if requested
            $this->checkForSchemaParam();

            //Validate feature enabled
            $this->validateProfileSyncFeature();

            //Send profiles
            $this->sendResponse();
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }

    /**
     * @return void
     */
    protected function checkForSchemaParam(): void
    {
        try {
            if (isset($this->queryParams[self::QUERY_PARAM_SCHEMA])) {
                /** @var Schema $profileSchema */
                $profileSchema = $this->module->helper->getService(HelperInterface::SERVICE_PROFILE_SCHEMA);
                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_200, $profileSchema->getDefinition()));
            }
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
            $response = $this->generateResponse(self::HTTP_CODE_200, $this->createResponseBody());
            $this->exitWithResponse($response);
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }

    /**
     * @return array
     */
    protected function createResponseBody(): array
    {
        try {
            $afterIdFromRequest = isset($this->queryParams[self::QUERY_PARAM_AFTER_ID]) ?
                (int) $this->queryParams[self::QUERY_PARAM_AFTER_ID] : 0;
            $profilesDataArr = $this->getProfilesDataArr($afterIdFromRequest);
            $afterIdFromDataArr = $profilesDataArr[self::QUERY_PARAM_AFTER_ID];

            $paramSelf = empty($afterIdFromRequest) ? [] : [self::QUERY_PARAM_AFTER_ID => $afterIdFromRequest];
            $paramNext = empty($afterIdFromDataArr) ? [] : [self::QUERY_PARAM_AFTER_ID => $afterIdFromDataArr];

            return [
                self::BODY_PARAM_LINKS => [
                    self::BODY_PARAM_LINKS_SELF => $this->buildLink($paramSelf, false),
                    self::BODY_PARAM_LINKS_NEXT => $this->buildLink($paramNext, true)
                ],
                self::BODY_PARAM_COUNT => count($profilesDataArr[self::JSON_BODY_PARAM_ITEMS]),
                self::BODY_PARAM_TOTAL => $this->getTotalCount(),
                self::JSON_BODY_PARAM_ITEMS => $profilesDataArr[self::JSON_BODY_PARAM_ITEMS]
            ];
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
            return [];
        }
    }

    /**
     * @param array $param
     * @param bool $next
     *
     * @return string
     */
    protected function buildLink(array $param, bool $next): string
    {
        if ($next && empty($param)) {
            return '';
        }

        $param[self::QUERY_PARAM_CONTEXT_IDS] = $this->queryParams[self::QUERY_PARAM_CONTEXT_IDS];

        /** @var LinkContext $linkContext */
        $linkContext = $this->module->helper->getService(HelperInterface::SERVICE_CONTEXT_LINK);
        return $linkContext->getModuleLink(
            SetupInterface::API_PROFILES_CONTROLLER,
            $param,
            true,
            null,
            $this->shopId ?: $this->configs->getDefaultShopId()
        );
    }

    /**
     * @param int $afterId
     *
     * @return array
     */
    protected function getProfilesDataArr(int $afterId): array
    {
        $items = [];

        try {
            /** @var EntityHelper $entityHelper */
            $entityHelper = $this->module->helper->getService(HelperInterface::SERVICE_HELPER_ENTITY);
            $profiles = $this->getProfileRepository()
                ->findBySyncStatusForGivenShop(
                    [EI::SS_JUSTIN],
                    $this->module->helper->getStoreIdArrFromContext((int) $this->groupId, (int) $this->shopId),
                    $afterId
                );

            if (is_array($profiles)) {
                $inclEvents = isset($this->queryParams[self::QUERY_PARAM_INCLUDE_EVENTS]);

                foreach ($profiles as $profile) {
                    $item = $entityHelper->getProfileDataArrForExport(
                        $profile,
                        ($inclEvents && $this->configs->getEventSyncFlag($this->groupId, $this->shopId))
                    );
                    if (! empty($item)) {
                        $items[$profile->getId()] = $item;
                    }
                }
            }

        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }

        return [self::JSON_BODY_PARAM_ITEMS => $items, self::QUERY_PARAM_AFTER_ID => (int) array_key_last($items)];
    }

    /**
     * @return int|null
     */
    protected function getTotalCount(): ?int
    {
        try {
            /** @var EntityHelper $entityHelper */
            $entityHelper = $this->module->helper->getService(HelperInterface::SERVICE_HELPER_ENTITY);
            return $entityHelper->getProfileRepository()
                ->getTotalCountBySyncStatusAndShop(
                    [EI::SS_JUSTIN],
                    $this->module->helper->getStoreIdArrFromContext((int) $this->groupId, (int) $this->shopId)
                );
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
            return null;
        }
    }
}
