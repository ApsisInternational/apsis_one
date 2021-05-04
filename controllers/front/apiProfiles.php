<?php

use Apsis\One\Controller\AbstractApiController;
use Apsis\One\Model\Profile\Schema;

class apsis_OneApiProfilesModuleFrontController extends AbstractApiController
{
    const QUERY_PARAM_SCHEMA = 'schema';
    const QUERY_PARAM_AFTER_ID = 'after_id';
    const JSON_BODY_PARAM_ITEMS = 'items';

    /**
     * @var string
     */
    protected $validRequestMethod = AbstractApiController::HTTP_GET;

    /**
     * @var array
     */
    protected $validQueryParams = [
        AbstractApiController::QUERY_PARAM_CONTEXT_IDS => AbstractApiController::DATA_TYPE_STRING
    ];

    /**
     * @var array
     */
    protected $optionalQueryParams = [
        self::QUERY_PARAM_SCHEMA => AbstractApiController::DATA_TYPE_INT,
        self::QUERY_PARAM_AFTER_ID => AbstractApiController::DATA_TYPE_INT
    ];

    /**
     * @var array
     */
    protected $optionalQueryParamIgnoreRelations = [
        self::QUERY_PARAM_SCHEMA => [
            AbstractApiController::PARAM_TYPE_QUERY => [AbstractApiController::QUERY_PARAM_CONTEXT_IDS]
        ]
    ];

    public function init()
    {
        try {
            parent::init();
            $this->handleRequest();
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    protected function handleRequest()
    {
        try {
            //Send schema if requested
            $this->checkForSchemaParam();

            //Validate feature enabled
            $this->validateProfileSyncFeature();

            //Send profiles
            $this->getProfiles();
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    private function checkForSchemaParam()
    {
        try {
            if (isset($this->queryParams[self::QUERY_PARAM_SCHEMA])) {
                /** @var Schema $profileSchema */
                $profileSchema = $this->module->getService('apsis_one.profile.schema');
                $this->exitWithResponse(
                    $this->generateResponse(AbstractApiController::HTTP_CODE_200, $profileSchema->getProfileSchema())
                );
            }
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    private function getProfiles()
    {
        try {
            $response = $this->generateResponse(AbstractApiController::HTTP_CODE_200, $this->createResponseBody());
            $this->exitWithResponse($response);
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    /**
     * @return array
     */
    private function createResponseBody()
    {
        try {
            $profilesDataArr = $this->getProfilesDataArr();

            //@toDo create url using prestashop link class
            $urlString = 'DOMAIN_NAME/module/apsis_one/apiprofiles';
            $paramString = '?' . self::QUERY_PARAM_AFTER_ID . '=%d';

            $self = empty($afterIdFromRequest = (int) Tools::getValue(self::QUERY_PARAM_AFTER_ID)) ?
                $urlString : $urlString . sprintf($paramString, $afterIdFromRequest);

            $next = empty($profilesDataArr[self::QUERY_PARAM_AFTER_ID]) ?
                '' : $urlString . sprintf($paramString, $profilesDataArr[self::QUERY_PARAM_AFTER_ID]);

            return [
                'links' => [
                    'self' => $self,
                    'next' => $next
                ],
                'count' => count($profilesDataArr[self::JSON_BODY_PARAM_ITEMS]),
                'total' => $this->getTotalCount(),
                'items' => $profilesDataArr[self::JSON_BODY_PARAM_ITEMS]
            ];
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
        return [];
    }

    private function getProfilesDataArr()
    {
        //@toDo fetch profile from service class
        $items = [];
        try {
            //$afterId = Tools::getValue(self::QUERY_PARAM_AFTER_ID);
            /** START - dummy test data for testing */
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

            /** @var Apsis\One\Model\Profile\Data $container */
            $container = $this->module->getService('apsis_one.profile.container');
            foreach ($profiles as $profile) {
                $items[] = $container->setObject($profile)->getProfileData();
            }

            /** END */

        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }

        return [self::JSON_BODY_PARAM_ITEMS => $items, self::QUERY_PARAM_AFTER_ID => 0];
    }

    /**
     * @return int
     */
    private function getTotalCount()
    {
        try {
            //@toDo fetch from db
            return 2;
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
            return 0;
        }
    }
}