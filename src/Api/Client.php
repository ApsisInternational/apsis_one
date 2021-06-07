<?php

namespace Apsis\One\Api;

class Client extends AbstractHttpRest
{
    /**
     * @var array
     */
    private $cacheContainer = [];

    /**
     * @param string $key
     *
     * @return mixed
     */
    private function getFromCacheContainer(string $key)
    {
        if (strlen($key) && isset($this->cacheContainer[$key])) {
            if ((bool) getenv('APSIS_DEVELOPER')) {
                $this->helper->logDebugMsg('API response from cache container.', ['URL' => $key]);
            }

            return $this->cacheContainer[$key];
        }
        return null;
    }

    /**
     * @param string $method
     * @param array $methodParams
     *
     * @return string|false
     */
    private function buildKeyForCacheContainer(string $method, array $methodParams): ?string
    {
        return filter_var(implode(".", array_filter(array_merge([$method], $methodParams))), FILTER_SANITIZE_STRING);
    }

    /**
     * @param string $fromMethod
     * @param string $key
     *
     * @return mixed
     */
    private function executeRequestAndReturnResponse(string $fromMethod, string $key = '')
    {
        $response = $this->processResponse($this->execute(), $fromMethod);
        return strlen($key) ? $this->cacheContainer[$key] = $response : $response;
    }

    /**
     * SECURITY: Get access token
     *
     * Use client ID and client secret obtained when creating an API key in your APSIS One account to request an
     * OAuth 2.0 access token. Provide that token as Authorization: Bearer <access token> header when making calls to
     * other endpoints of this API.
     *
     *
     * @return mixed
     */
    public function getAccessToken()
    {
        $this->setUrl('/oauth/token')
            ->setVerb(self::VERB_POST)
            ->buildBodyForGetAccessTokenCall();
        return $this->executeRequestAndReturnResponse(__METHOD__);
    }

    /**
     * DEFINITIONS: Get keyspaces
     *
     * Get all registered keyspaces.
     *
     * @return mixed
     */
    public function getKeySpaces()
    {
        $key = $this->buildKeyForCacheContainer(__FUNCTION__, []);
        if ($fromCache = $this->getFromCacheContainer($key)) {
            return $fromCache;
        }

        $this->setUrl('/audience/keyspaces')
            ->setVerb(self::VERB_GET);
        return $this->executeRequestAndReturnResponse( __METHOD__, $key);
    }

    /**
     * DEFINITIONS: Get sections
     *
     * Get all sections on the APSIS One account.
     *
     * @return mixed
     */
    public function getSections()
    {
        $key = $this->buildKeyForCacheContainer(__FUNCTION__, []);
        if ($fromCache = $this->getFromCacheContainer($key)) {
            return $fromCache;
        }

        $this->setUrl('/audience/sections')
            ->setVerb(self::VERB_GET);
        return $this->executeRequestAndReturnResponse(__METHOD__, $key);
    }

    /**
     * DEFINITIONS: Get attributes
     *
     * Gets all attributes within a specific section. Includes default and custom attributes. When any ecommerce
     * integration is connected to the specified section then also ecommerce attributes are returned.
     *
     * @param string $sectionDiscriminator
     *
     * @return mixed
     */
    public function getAttributes(string $sectionDiscriminator)
    {
        $key = $this->buildKeyForCacheContainer(__FUNCTION__, func_get_args());
        if ($fromCache = $this->getFromCacheContainer($key)) {
            return $fromCache;
        }

        $this->setUrl('/audience/sections/' . $sectionDiscriminator . '/attributes')
            ->setVerb(self::VERB_GET);
        return $this->executeRequestAndReturnResponse(__METHOD__, $key);
    }

    /**
     * DEFINITIONS: Get consent lists
     *
     * Get all Consent lists within a specific section.
     *
     * @param string $sectionDiscriminator
     *
     * @return mixed
     */
    public function getConsentLists(string $sectionDiscriminator)
    {
        $key = $this->buildKeyForCacheContainer(__FUNCTION__, func_get_args());
        if ($fromCache = $this->getFromCacheContainer($key)) {
            return $fromCache;
        }

        $this->setUrl('/audience/sections/' . $sectionDiscriminator . '/consent-lists')
            ->setVerb(self::VERB_GET);
        return $this->executeRequestAndReturnResponse(__METHOD__, $key);
    }

    /**
     * DEFINITIONS: Get topics
     *
     * Get all topics on a consent list
     *
     * @param string $sectionDiscriminator
     * @param string $consentListDiscriminator
     *
     * @return mixed
     */
    public function getTopics(string $sectionDiscriminator, string $consentListDiscriminator)
    {
        $key = $this->buildKeyForCacheContainer(__FUNCTION__, func_get_args());
        if ($fromCache = $this->getFromCacheContainer($key)) {
            return $fromCache;
        }

        $this->setUrl('/audience/sections/' . $sectionDiscriminator . '/consent-lists/' .
            $consentListDiscriminator . '/topics')
            ->setVerb(self::VERB_GET);
        return $this->executeRequestAndReturnResponse(__METHOD__, $key);
    }

    /**
     * DEFINITIONS: Get events
     *
     * Get all events defined within a specific section.
     *
     * @param string $sectionDiscriminator
     *
     * @return mixed
     */
    public function getEvents(string $sectionDiscriminator)
    {
        $key = $this->buildKeyForCacheContainer(__FUNCTION__, func_get_args());
        if ($fromCache = $this->getFromCacheContainer($key)) {
            return $fromCache;
        }

        $this->setUrl('/audience/sections/' . $sectionDiscriminator . '/events')
            ->setVerb(self::VERB_GET);
        return $this->executeRequestAndReturnResponse(__METHOD__, $key);
    }

    /**
     * PROFILES: Set attributes for a profile
     *
     * Updates profile attribute values using their version IDs as keys. Permits changes to default and custom
     * attributes. When any ecommerce integration is connected to the specified section then also ecommerce attributes
     * can be modified.
     * Content must follow JSON Merge Patch specs.
     * The maximum data payload size for requests to this endpoint is 100KB.
     *
     * @param string $keySpaceDiscriminator
     * @param string $profileKey
     * @param string $sectionDiscriminator
     * @param array $attributes
     *
     * @return mixed
     */
    public function addAttributesToProfile(
        string $keySpaceDiscriminator,
        string $profileKey,
        string $sectionDiscriminator,
        array $attributes
    ) {
        $this->setUrl('/audience/keyspaces/' . $keySpaceDiscriminator . '/profiles/' . $profileKey .
            '/sections/' . $sectionDiscriminator . '/attributes')
            ->setVerb(self::VERB_PATCH)
            ->buildBody($attributes);
        return $this->executeRequestAndReturnResponse(__METHOD__);
    }

    /**
     *  PROFILES: Clear attribute value for a profile
     *
     * @param string $keySpaceDiscriminator
     * @param string $profileKey
     * @param string $sectionDiscriminator
     * @param string $versionId
     *
     * @return mixed
     */
    public function clearProfileAttribute(
        string $keySpaceDiscriminator,
        string $profileKey,
        string $sectionDiscriminator,
        string $versionId
    ) {
        $this->setUrl('/audience/keyspaces/' . $keySpaceDiscriminator . '/profiles/' . $profileKey .
            '/sections/' . $sectionDiscriminator . '/attributes/' . $versionId)
            ->setVerb(self::VERB_DELETE);
        return $this->executeRequestAndReturnResponse(__METHOD__);
    }

    /**
     * PROFILES: Add events to a profile
     *
     * The maximum data payload size for requests to this endpoint is 100KB
     *
     * @param string $keySpaceDiscriminator
     * @param string $profileKey
     * @param string $sectionDiscriminator
     * @param array $events
     *
     * @return mixed
     */
    public function addEventsToProfile(
        string $keySpaceDiscriminator,
        string $profileKey,
        string $sectionDiscriminator,
        array $events
    ) {
        $this->setUrl('/audience/keyspaces/' . $keySpaceDiscriminator . '/profiles/' . $profileKey .
            '/sections/' . $sectionDiscriminator . '/events')
            ->setVerb(self::VERB_POST)
            ->buildBody(['items' => $events]);
        return $this->executeRequestAndReturnResponse(__METHOD__);
    }

    /**
     * PROFILES: Subscribe profile to topic
     *
     * @param string $keySpaceDiscriminator
     * @param string $profileKey
     * @param string $sectionDiscriminator
     * @param string $consentListDiscriminator
     * @param string $topicDiscriminator
     *
     * @return mixed
     */
    public function subscribeProfileToTopic(
        string $keySpaceDiscriminator,
        string $profileKey,
        string $sectionDiscriminator,
        string $consentListDiscriminator,
        string $topicDiscriminator
    ) {
        $this->setUrl('/audience/keyspaces/' . $keySpaceDiscriminator . '/profiles/' . $profileKey .
            '/sections/' . $sectionDiscriminator . '/subscriptions')
            ->setVerb(self::VERB_POST)
            ->buildBody(
                [
                    'consent_list_discriminator' => $consentListDiscriminator,
                    'topic_discriminator' => $topicDiscriminator
                ]
            );
        return $this->executeRequestAndReturnResponse(__METHOD__);
    }

    /**
     * PROFILES: Merge two profiles
     *
     * Merges two profiles designated in the body using keyspace discriminator and profile key. As a result of the
     * merge, both profile keys in both keyspaces will point to the same physical profile. Merging profiles using
     * profile keys from different keyspaces is supported. Merge is both associative and commutative so you can do
     * (a + b) + c if you need to merge more than two profiles.
     * If any of the merged profiles does not exist then it is created along the way. Also, if one of the merged
     * profiles is locked then the other profile will be locked as well if the merge succeeds.
     *
     * @param array $keySpacesToMerge
     *
     * @return mixed
     */
    public function mergeProfile(array $keySpacesToMerge)
    {
        $this->setUrl('/audience/profiles/merges')
            ->setVerb(self::VERB_PUT)
            ->buildBody(['profiles' => $keySpacesToMerge]);
        return $this->executeRequestAndReturnResponse(__METHOD__);
    }

    /**
     * Delete a profile
     *
     * Profile will be permanently deleted along with its consents and events.
     * This operation is permanent and irreversible.
     *
     * @param string $keySpaceDiscriminator
     * @param string $profileKey
     *
     * @return mixed
     */
    public function deleteProfile(string $keySpaceDiscriminator, string $profileKey)
    {
        $this->setUrl('/audience/keyspaces/' . $keySpaceDiscriminator . '/profiles/' . $profileKey)
            ->setVerb(self::VERB_DELETE);
        return $this->executeRequestAndReturnResponse(__METHOD__);
    }

    /**
     * CONSENTS: Create consent
     *
     * @param string $channelDiscriminator
     * @param string $address
     * @param string $sectionDiscriminator
     * @param string $consentListDiscriminator
     * @param string $topicDiscriminator
     * @param string $type
     *
     * @return mixed
     */
    public function createConsent(
        string $channelDiscriminator,
        string $address,
        string $sectionDiscriminator,
        string $consentListDiscriminator,
        string $topicDiscriminator,
        string $type
    ) {
        $body = [
            'section_discriminator' => $sectionDiscriminator,
            'type' => $type
        ];
        if (strlen($consentListDiscriminator)) {
            $body['consent_list_discriminator'] = $consentListDiscriminator;
        }
        if (strlen($topicDiscriminator)) {
            $body['topic_discriminator'] = $topicDiscriminator;
        }
        $this->setUrl('/audience/channels/' . $channelDiscriminator . '/addresses/' . $address . '/consents')
            ->setVerb(self::VERB_POST)
            ->buildBody($body);
        return $this->executeRequestAndReturnResponse(__METHOD__);
    }

    /**
     * CONSENTS: Get opt-in consents
     *
     * Returns all opt-in consented topics for a given address.
     * Verifies whether an opt-in consent (topic-level opt-in with no opt-out on higher level) exists for given address.
     * If no opt-in consent exists for a given address then an empty list is returned.
     *
     * @param string $channelDiscriminator
     * @param string $address
     * @param string $sectionDiscriminator
     * @param string $consentListDiscriminator
     *
     * @return mixed
     */
    public function getOptInConsents(
        string $channelDiscriminator,
        string $address,
        string $sectionDiscriminator,
        string $consentListDiscriminator
    ) {
        $this->setUrl('/audience/channels/' . $channelDiscriminator . '/addresses/' . $address . '/consents/sections/' .
            $sectionDiscriminator . '/consent-lists/' . $consentListDiscriminator . '/evaluations')
            ->setVerb(self::VERB_GET);
        return $this->executeRequestAndReturnResponse(__METHOD__);
    }

    /**
     * INTEGRATIONS: Justin Delta Sync Manager
     *
     * Schedule an update of profiles
     *
     * @param string $sectionDiscriminator
     * @param array $data
     * @param string $integrationName
     *
     * @return mixed
     */
    public function insertOrUpdateProfiles(
        string $sectionDiscriminator,
        array $data,
        string $integrationName = 'prestashop'
    ) {
        $this->setUrl('/audience/sections/' . $sectionDiscriminator . '/integrations/' . $integrationName . '/updates')
            ->setVerb(self::VERB_POST)
            ->buildBody($data);
        return $this->executeRequestAndReturnResponse(__METHOD__);
    }
}
