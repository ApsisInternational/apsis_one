<?php

namespace Apsis\One\Controller;

interface ApiControllerInterface
{
    const POST_KEY_UPDATE = 'APSIS_UPDATE';

    /** CURL OPT VALUES */
    const CURL_REQUEST_TIMOUT = 30;
    const CURL_REQUEST_MAX_REDIRECTS = 10;

    /**
     * Data types
     */
    const DATA_TYPE_INT = 'integer';
    const DATA_TYPE_STRING = 'string';

    /** PARAM TYPES */
    const PARAM_TYPE_QUERY = 'query';
    const PARAM_TYPE_BODY = 'body';

    /** QUERY PARAMS */
    const QUERY_PARAM_CONTEXT_IDS = 'context_ids';
    const QUERY_PARAM_RESET = 'reset';
    const QUERY_PARAM_SCHEMA = 'schema';
    const QUERY_PARAM_TOKEN = 'token';
    const QUERY_PARAM_AFTER_ID = 'after_id';
    const QUERY_PARAM_INCLUDE_EVENTS = 'include_events';
    const QUERY_PARAM_AFTER_DATETIME = 'after_datetime';
    const QUERY_PARAM_BEFORE_DATETIME = 'before_datetime';

    /** BODY PARAMS */
    const JSON_BODY_PARAM_ITEMS = 'items';
    const BODY_PARAM_PROFILE_KEY = 'profile_key';
    const BODY_PARAM_CONSENT_NAME = 'consent_name';
    const BODY_PARAM_TOTAL = 'total';
    const BODY_PARAM_COUNT = 'count';
    const BODY_PARAM_LINKS = 'links';
    const BODY_PARAM_LINKS_SELF = 'self';
    const BODY_PARAM_LINKS_NEXT = 'next';

    /** HTTP Codes  */
    const HTTP_CODE_200 = 200;
    const HTTP_CODE_204 = 204;
    const HTTP_CODE_400 = 400;
    const HTTP_CODE_401 = 401;
    const HTTP_CODE_403 = 403;
    const HTTP_CODE_404 = 404;
    const HTTP_CODE_405 = 405;
    const HTTP_CODE_408 = 408;
    const HTTP_CODE_409 = 409;
    const HTTP_CODE_429 = 429;
    const HTTP_CODE_500 = 500;
    const HTTP_CODE_501 = 501;
    const HTTP_CODE_503 = 503;

    const HTTP_CODE_TO_TEXT_MAP = [
        self::HTTP_CODE_200 => 'OK',
        self::HTTP_CODE_204 => 'No Content',
        self::HTTP_CODE_400 => 'Bad Request',
        self::HTTP_CODE_401 => 'Unauthorized',
        self::HTTP_CODE_403 => 'Forbidden',
        self::HTTP_CODE_404 => 'Not Found',
        self::HTTP_CODE_405 => 'Method Not Allowed',
        self::HTTP_CODE_408 => 'Request Timeout',
        self::HTTP_CODE_409 => 'Conflict',
        self::HTTP_CODE_429 => 'Too Many Requests',
        self::HTTP_CODE_500 => 'Internal Server Error',
        self::HTTP_CODE_501 => 'Not Implemented',
        self::HTTP_CODE_503 => 'Service Unavailable',
    ];

    /** HTTP VERBS  */
    const VERB_GET = 'GET';
    const VERB_POST = 'POST';
    const VERB_PUT = 'PUT';
    const VERB_DELETE = 'DELETE';
    const VERB_PATCH = 'PATCH';

    /** HTTP CODES REQUIRES BODY  */
    const REQUEST_BODY_FOR_HTTP_METHOD = [self::VERB_POST, self::VERB_PATCH];

    /** HTTP REQUEST CODES TO RETRY  */
    const HTTP_ERROR_CODES_TO_RETRY = [
        self::HTTP_CODE_500,
        self::HTTP_CODE_501,
        self::HTTP_CODE_503,
        self::HTTP_CODE_408,
        self::HTTP_CODE_429
    ];

    /** HTTP CODES TO DISABLE FEATURES */
    const HTTP_CODES_DISABLE_MODULE = [self::HTTP_CODE_400, self::HTTP_CODE_401, self::HTTP_CODE_403];

    /** HTTP CODES TO FORCE GENERATE TOKEN */
    const HTTP_CODES_FORCE_GENERATE_TOKEN = [self::HTTP_CODE_401, self::HTTP_CODE_403];

    /**
     * @return void
     */
    public function init() : void;
}
