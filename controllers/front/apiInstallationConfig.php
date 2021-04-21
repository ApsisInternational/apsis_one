<?php

use Apsis\One\Controller\AbstractApiController;

class apsis_OneApiInstallationConfigModuleFrontController extends AbstractApiController
{
    /**
     * @var string
     */
    protected $validRequestMethod = 'POST';

    /**
     * @var string[]
     */
    protected $validBodyParams = [
        'context_ids',
        'client_id',
        'client_secret',
        'account_id',
        'section_discriminator',
        'keyspace_discriminator',
        'api_base_url'
    ];

    public function init()
    {
        parent::init();

        $this->processRequest();
    }

    private function processRequest()
    {
        $contextIds = explode(',', $this->bodyParams['context_ids']);
        if (count($contextIds) === 2) {
            $groupId = (int) $contextIds[0];
            $shopId = (int) $contextIds[1];
            unset($this->bodyParams['context_ids']);

            $this->configurationRepository->saveInstallationConfigs($this->bodyParams, $groupId, $shopId) ?
                $this->exitWithResponse($this->generateResponse(201)) :
                $this->exitWithResponse($this->generateResponse(500, [], 'Unable to save configurations.'));
        } else {
            $this->exitWithResponse($this->generateResponse(400, [], 'Invalid context ids.'));
        }
    }
}