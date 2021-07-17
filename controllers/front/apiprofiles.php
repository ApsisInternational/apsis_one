<?php

use Apsis\One\Controller\AbstractApiController;
use Apsis\One\Helper\HelperInterface;
use Apsis\One\Model\Profile\Schema;
use Apsis\One\Module\SetupInterface;
use Apsis\One\Context\LinkContext;

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
            self::QUERY_PARAM_AFTER_ID => self::DATA_TYPE_INT
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
            $profilesDataArr = $this->getProfilesDataArr();
            $afterIdFromRequest = (int) Tools::getValue(self::QUERY_PARAM_AFTER_ID);
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
        }
        return [];
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

        /** @var LinkContext $linkContext */
        $linkContext = $this->module->helper->getService(HelperInterface::SERVICE_CONTEXT_LINK);
        return $linkContext->getModuleLink(
            SetupInterface::API_STORES_CONTROLLER_FILENAME,
            $param,
            null,
            null,
            $this->shopId ?: $this->configs->getDefaultShopId()
        );
    }

    /**
     * @return array
     */
    protected function getProfilesDataArr(): array
    {
        // TODO: get profiles from profile entity
        return [self::JSON_BODY_PARAM_ITEMS => [], self::QUERY_PARAM_AFTER_ID => 0];

        /** START - dummy test data for testing
        $items = [];
        try {
            $profiles = [
                (object)[
                    'profileId' => 'a2720191-1cc6-11eb-9a2c-107d1a24f935',
                    'customerId' => null,
                    'shopId' => 1,
                    'shopGroupId' => 1,
                    'shopName' => 'Some Name',
                    'shopGroupName' => 'Some Group',
                    'email' => 'one@example.com',
                    'subscriptionId' => 1,
                    'isSubscribedToNewsletter' => true,
                    'newsletterDateAdded' => 1619793187
                ],
                (object)[
                    'profileId' => 'b2720191-1cc6-11eb-9a2c-107d1a24f935',
                    'customerId' => 1,
                    'shopId' => 1,
                    'shopGroupId' => 1,
                    'shopName' => 'Some Name',
                    'shopGroupName' => 'Some Group',
                    'email' => 'two@example.com',
                    'subscriptionId' => 2,
                    'isSubscribedToNewsletter' => true,
                    'newsletterDateAdded' => 1619713187
                ]
            ];

            //@var Apsis\One\Model\Profile\Data $container
            $schema = $this->module->helper->getService('apsis_one.profile.schema');
            $container = $this->module->helper->getService('apsis_one.profile.container');
            foreach ($profiles as $profile) {
                $items[] = $container->setObject($profile, $schema)->getData();
            }

        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }

        return [self::JSON_BODY_PARAM_ITEMS => $items, self::QUERY_PARAM_AFTER_ID => 2];
         **/
    }

    /**
     * @return int
     */
    protected function getTotalCount(): int
    {
        // TODO: fetch from db
        return 0;
        /**
        try {
            return 0;
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
            return 0;
        }**/
    }
}