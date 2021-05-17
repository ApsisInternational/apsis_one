<?php

namespace Apsis\One\Api;

use Apsis\One\Module\SetupInterface;
use stdClass;

class Client extends AbstractHttpRest
{
    /**
     * SECURITY: Get access token
     *
     * Use client ID and client secret obtained when creating an API key in your APSIS One account to request an
     * OAuth 2.0 access token. Provide that token as Authorization: Bearer <access token> header when making calls to
     * other endpoints of this API.
     *
     * @param string $clientId
     * @param string $clientSecret
     *
     * @return bool|int|stdClass|string|null
     */
    public function getAccessToken(string $clientId, string $clientSecret)
    {
        $this->setUrl('/oauth/token')
            ->setVerb(self::VERB_POST)
            ->buildBody([
                'grant_type' => 'client_credentials',
                SetupInterface::INSTALLATION_CONFIG_CLIENT_ID => $clientId,
                SetupInterface::INSTALLATION_CONFIG_CLIENT_SECRET => $clientSecret
            ]);
        return $this->processResponse($this->execute(), __METHOD__);
    }

    /**
     * DEFINITIONS: Get keyspaces
     *
     * Get all registered keyspaces.
     *
     * @return bool|int|stdClass|string|null
     */
    public function getKeySpaces()
    {
        $this->setUrl('/audience/keyspaces')
            ->setVerb(self::VERB_GET);
        return $this->processResponse($this->execute(), __METHOD__);
    }

    /**
     * DEFINITIONS: Get channels
     *
     * Get all available communication channels.
     *
     * @return bool|int|stdClass|string|null
     */
    public function getChannels()
    {
        $this->setUrl('/audience/channels')
            ->setVerb(self::VERB_GET);
        return $this->processResponse($this->execute(), __METHOD__);
    }

    /**
     * DEFINITIONS: Get sections
     *
     * Get all sections on the APSIS One account.
     *
     * @return bool|int|stdClass|string|null
     */
    public function getSections()
    {
        $this->setUrl('/audience/sections')
            ->setVerb(self::VERB_GET);
        return $this->processResponse($this->execute(), __METHOD__);
    }

    /**
     * DEFINITIONS: Get attributes
     *
     * Gets all attributes within a specific section. Includes default and custom attributes. When any ecommerce
     * integration is connected to the specified section then also ecommerce attributes are returned.
     *
     * @param string $sectionDiscriminator
     *
     * @return bool|int|stdClass|string|null
     */
    public function getAttributes(string $sectionDiscriminator)
    {
        $this->setUrl('/audience/sections/' . $sectionDiscriminator . '/attributes')
            ->setVerb(self::VERB_GET);
        return $this->processResponse($this->execute(), __METHOD__);
    }

    /**
     * DEFINITIONS: Get consent lists
     *
     * Get all Consent lists within a specific section.
     *
     * @param string $sectionDiscriminator
     *
     * @return bool|int|stdClass|string|null
     */
    public function getConsentLists(string $sectionDiscriminator)
    {
        $this->setUrl('/audience/sections/' . $sectionDiscriminator . '/consent-lists')
            ->setVerb(self::VERB_GET);
        return $this->processResponse($this->execute(), __METHOD__);
    }

    /**
     * DEFINITIONS: Get topics
     *
     * Get all topics on a consent list
     *
     * @param string $sectionDiscriminator
     * @param string $consentListDiscriminator
     *
     * @return bool|int|stdClass|string|null
     */
    public function getTopics(string $sectionDiscriminator, string $consentListDiscriminator)
    {
        $this->setUrl('/audience/sections/' . $sectionDiscriminator . '/consent-lists/' .
            $consentListDiscriminator . '/topics')
            ->setVerb(self::VERB_GET);
        return $this->processResponse($this->execute(), __METHOD__);
    }

    /**
     * DEFINITIONS: Get tags
     *
     * Get all tags defined within a specific section.
     *
     * @param string $sectionDiscriminator
     *
     * @return bool|int|stdClass|string|null
     */
    public function getTags(string $sectionDiscriminator)
    {
        $this->setUrl('/audience/sections/' . $sectionDiscriminator . '/tags')
            ->setVerb(self::VERB_GET);
        return $this->processResponse($this->execute(), __METHOD__);
    }

    /**
     * DEFINITIONS: Get events
     *
     * Get all events defined within a specific section.
     *
     * @param string $sectionDiscriminator
     *
     * @return bool|int|stdClass|string|null
     */
    public function getEvents(string $sectionDiscriminator)
    {
        $this->setUrl('/audience/sections/' . $sectionDiscriminator . '/events')
            ->setVerb(self::VERB_GET);
        return $this->processResponse($this->execute(), __METHOD__);
    }

    /**
     * DEFINITIONS: Get segments
     *
     * Get all segments.
     *
     * @return bool|int|stdClass|string|null
     */
    public function getSegments()
    {
        $this->setUrl('/audience/segments/')
            ->setVerb(self::VERB_GET);
        return $this->processResponse($this->execute(), __METHOD__);
    }

    /**
     * DEFINITIONS: Get segment
     *
     * Get a single segment.
     *
     * @param string $segmentDiscriminator
     *
     * @return bool|int|stdClass|string|null
     */
    public function getSegment(string $segmentDiscriminator)
    {
        $this->setUrl('/audience/segments/' . $segmentDiscriminator)
            ->setVerb(self::VERB_GET);
        return $this->processResponse($this->execute(), __METHOD__);
    }

    /**
     * DEFINITIONS: Get segment version
     *
     * Get specific segment version.
     *
     * @param string $segmentDiscriminator
     * @param string $versionId
     *
     * @return bool|int|stdClass|string|null
     */
    public function getSegmentVersion(string $segmentDiscriminator, string $versionId)
    {
        $this->setUrl('/audience/segments/' . $segmentDiscriminator . '/versions/' . $versionId)
            ->setVerb(self::VERB_GET);
        return $this->processResponse($this->execute(), __METHOD__);
    }

    /**
     * PROFILES: Add tags to a profile
     *
     * Content must follow JSON Merge Patch specs.
     * The maximum data payload size for requests to this endpoint is 100KB.
     *
     * @param string $keySpaceDiscriminator
     * @param string $profileKey
     * @param string $sectionDiscriminator
     * @param array $tags
     *
     * @return bool|int|stdClass|string|null
     */
    public function addTagsToProfile(
        string $keySpaceDiscriminator,
        string $profileKey,
        string $sectionDiscriminator,
        array $tags
    ) {
        $this->setUrl('/audience/keyspaces/' . $keySpaceDiscriminator . '/profiles/' . $profileKey .
            '/sections/' . $sectionDiscriminator . '/tags')
            ->setVerb(self::VERB_PATCH)
            ->buildBody($tags);
        return $this->processResponse($this->execute(), __METHOD__);
    }

    /**
     * PROFILES: Get all profile tags.
     *
     * @param string $keySpaceDiscriminator
     * @param string $profileKey
     * @param string $sectionDiscriminator
     *
     * @return bool|int|stdClass|string|null
     */
    public function getAllProfileTags(string $keySpaceDiscriminator, string $profileKey, string $sectionDiscriminator)
    {
        $this->setUrl('/audience/keyspaces/' . $keySpaceDiscriminator . '/profiles/' . $profileKey .
            '/sections/' . $sectionDiscriminator . '/tags')
            ->setVerb(self::VERB_GET);
        return $this->processResponse($this->execute(), __METHOD__);
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
     * @return bool|int|stdClass|string|null
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
        return $this->processResponse($this->execute(), __METHOD__);
    }

    /**
     *  PROFILES: Get all profile attributes
     *
     * Gets profile attribute values with their version IDs as keys. Exposes default and custom attributes.
     * When any ecommerce integration is connected to the specified section then also ecommerce attributes are returned.
     *
     * @param string $keySpaceDiscriminator
     * @param string $profileKey
     * @param string $sectionDiscriminator
     *
     * @return bool|int|stdClass|string|null
     */
    public function getAllProfileAttributes(
        string $keySpaceDiscriminator,
        string $profileKey,
        string $sectionDiscriminator
    ) {
        $this->setUrl('/audience/keyspaces/' . $keySpaceDiscriminator . '/profiles/' . $profileKey .
            '/sections/' . $sectionDiscriminator . '/attributes')
            ->setVerb(self::VERB_GET);
        return $this->processResponse($this->execute(), __METHOD__);
    }

    /**
     *  PROFILES: Clear attribute value for a profile
     *
     * @param string $keySpaceDiscriminator
     * @param string $profileKey
     * @param string $sectionDiscriminator
     * @param string $versionId
     *
     * @return bool|int|stdClass|string|null
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
        return $this->processResponse($this->execute(), __METHOD__);
    }

    /**
     * PROFILES: Get all profile events
     *
     * @param string $keySpaceDiscriminator
     * @param string $profileKey
     * @param string $sectionDiscriminator
     *
     * @return bool|int|stdClass|string|null
     */
    public function getProfileEvents(
        string $keySpaceDiscriminator,
        string $profileKey,
        string $sectionDiscriminator
    ) {
        $this->setUrl('/audience/keyspaces/' . $keySpaceDiscriminator . '/profiles/' . $profileKey .
            '/sections/' . $sectionDiscriminator . '/events')
            ->setVerb(self::VERB_GET);

        return $this->processResponse($this->execute(), __METHOD__);
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
     * @return bool|int|stdClass|string|null
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
        return $this->processResponse($this->execute(), __METHOD__);
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
     * @return bool|int|stdClass|string|null
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
        return $this->processResponse($this->execute(), __METHOD__);
    }

    /**
     * PROFILES: Get profile segments
     *
     * Returns a list of segments the defined profile belongs to.
     *
     * @param string $keySpaceDiscriminator
     * @param string $profileKey
     * @param array $segments
     * @param string $timeZone
     *
     * @return bool|int|stdClass|string|null
     */
    public function getProfileSegments(
        string $keySpaceDiscriminator,
        string $profileKey,
        array $segments,
        string $timeZone
    ) {
        $this->setUrl('/audience/keyspaces/' . $keySpaceDiscriminator . '/profiles/' . $profileKey .
            '/evaluations')
            ->setVerb(self::VERB_POST)
            ->buildBody(['segments' => $segments, 'time_zone' => $timeZone]);

        return $this->processResponse($this->execute(), __METHOD__);
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
     * @return bool|int|stdClass|string|null
     */
    public function mergeProfile(array $keySpacesToMerge)
    {
        $this->setUrl('/audience/profiles/merges')
            ->setVerb(self::VERB_PUT)
            ->buildBody(['profiles' => $keySpacesToMerge]);
        return $this->processResponse($this->execute(), __METHOD__);
    }

    /**
     * PROFILES: Lock a profile
     *
     * Profile will be locked and its data permanently deleted. Profile lock is permanent and irreversible.
     * APSIS One will generate an encrypted, anonymous ID for the profile and block any future attempts of adding a
     * profile matching this ID.
     * It is not possible to import or create a locked profile nor for them to opt-in again. This applies to all
     * keyspaces.
     *
     * @param string $keySpaceDiscriminator
     * @param string $profileKey
     *
     * @return bool|int|stdClass|string|null
     */
    public function lockProfile(string $keySpaceDiscriminator, string $profileKey)
    {
        $this->setUrl('/audience/keyspaces/' . $keySpaceDiscriminator . '/profiles/' . $profileKey . '/locks')
            ->setVerb(self::VERB_PUT);
        return $this->processResponse($this->execute(), __METHOD__);
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
     * @return bool|int|stdClass|string|null
     */
    public function deleteProfile(string $keySpaceDiscriminator, string $profileKey)
    {
        $this->setUrl('/audience/keyspaces/' . $keySpaceDiscriminator . '/profiles/' . $profileKey)
            ->setVerb(self::VERB_DELETE);
        return $this->processResponse($this->execute(), __METHOD__);
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
     * @return bool|int|stdClass|string|null
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
        return $this->processResponse($this->execute(), __METHOD__);
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
     * @return bool|int|stdClass|string|null
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
        return $this->processResponse($this->execute(), __METHOD__);
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
     * @return bool|int|stdClass|string|null
     */
    public function insertOrUpdateProfiles(
        string $sectionDiscriminator,
        array $data,
        string $integrationName = 'prestashop'
    ) {
        $this->setUrl('/audience/sections/' . $sectionDiscriminator . '/integrations/' . $integrationName . '/updates')
            ->setVerb(self::VERB_POST)
            ->buildBody($data);
        return $this->processResponse($this->execute(), __METHOD__);
    }
}
