<?php

namespace Apsis\One\Api;

use Apsis\One\Controller\ApiControllerInterface;
use Apsis\One\Helper\HelperInterface;
use Exception;
use stdClass;

/**
 * Rest class to make cURL requests.
 */
abstract class AbstractHttpRest implements ApiControllerInterface
{
    /**
     * @var string
     */
    protected $hostName;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $verb;

    /**
     * @var string
     */
    protected $requestBody;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var null|stdClass
     */
    protected $responseBody;

    /**
     * @var null|array
     */
    protected $responseInfo;

    /**
     * @var HelperInterface
     */
    protected $helper;

    /**
     * @var string
     */
    protected $curlError;

    /**
     * Rest constructor.
     *
     * @param HelperInterface $helper
     * @param string $host
     * @param string $token
     * @param bool $isTokenNeeded
     *
     * @throws Exception
     */
    public function __construct(HelperInterface $helper, string $host, string $token = '', bool $isTokenNeeded = true)
    {
        if (empty($host)) {
            throw new Exception('Host cannot be empty', self::HTTP_CODE_500);
        }

        if ($isTokenNeeded && empty($token)) {
            throw new Exception('Token cannot be empty', self::HTTP_CODE_500);
        }

        if (function_exists('curl_init') === false) {
            throw new Exception('cURL is not enabled', self::HTTP_CODE_500);
        }

        $this->helper = $helper;
        $this->hostName = $host;
        $this->token = $token;
    }

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $this->responseBody = null;
        $this->responseInfo = null;
        $this->curlError = '';
    }

    /**
     * @return null|stdClass
     */
    protected function execute(): ?stdClass
    {
        $this->init();

        $ch = curl_init();
        if ($ch === false) {
            $this->helper->logErrorMessage(__METHOD__, 'Unable to initiate cURL resource');
            return $this->responseBody;
        }

        try {
            switch (strtoupper($this->verb)) {
                case self::VERB_GET:
                    $this->executeGet($ch);
                    break;
                case self::VERB_POST:
                    $this->executePost($ch);
                    break;
                case self::VERB_PATCH:
                    $this->executePatch($ch);
                    break;
                case self::VERB_PUT:
                    $this->executePut($ch);
                    break;
                case self::VERB_DELETE:
                    $this->executeDelete($ch);
                    break;
                default:
                    $error = __METHOD__ . ' : Current verb (' . $this->verb . ') is an invalid REST verb.';
                    $this->helper->logErrorMessage(__METHOD__, $error);
                    curl_close($ch);
            }
        } catch (Exception $e) {
            curl_close($ch);
            $this->helper->logErrorMessage(__METHOD__, $e->getMessage(), $e->getTraceAsString());
        }
        return $this->responseBody;
    }

    /**
     * Execute curl get request.
     *
     * @param resource $ch
     *
     * @return void
     *
     * @throws Exception
     */
    protected function executeGet($ch): void
    {
        $headers = [
            'Accept: application/json'
        ];
        $this->doExecute($ch, $headers);
    }

    /**
     * @param resource $ch
     * @param array $headers
     *
     * @return void
     *
     * @throws Exception
     */
    protected function executePostPutPatch($ch, array $headers): void
    {
        if (! is_string($this->requestBody) || empty($this->requestBody)) {
            throw new Exception('Invalid request body', self::HTTP_CODE_500);
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestBody);
        $this->doExecute($ch, $headers);
    }

    /**
     * Execute post request.
     *
     * @param resource $ch
     *
     * @return void
     *
     * @throws Exception
     */
    protected function executePost($ch): void
    {
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];
        $this->executePostPutPatch($ch, $headers);
    }

    /**
     * Execute patch request.
     *
     * @param resource $ch
     *
     * @return void
     *
     * @throws Exception
     */
    protected function executePatch($ch): void
    {
        $headers = [
            'Accept: application/problem+json',
            'Content-Type: application/merge-patch+json'
        ];
        $this->executePostPutPatch($ch, $headers);
    }

    /**
     * Execute put request.
     *
     * @param resource $ch
     *
     * @return void
     *
     * @throws Exception
     */
    protected function executePut($ch): void
    {
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];
        $this->executePostPutPatch($ch, $headers);
    }

    /**
     * Execute delete request.
     *
     * @param resource $ch
     *
     * @return void
     *
     * @throws Exception
     */
    protected function executeDelete($ch): void
    {
        $headers = [
            'Accept: application/problem+json'
        ];
        $this->doExecute($ch, $headers);
    }

    /**
     * Execute request.
     *
     * @param resource $ch
     * @param array headers
     *
     * @return void
     *
     * @throws Exception
     */
    protected function doExecute(&$ch, array $headers): void
    {
        $this->setCurlOpts($ch, $headers);
        $this->responseBody = json_decode(curl_exec($ch));
        $this->responseInfo = curl_getinfo($ch);
        $this->curlError = curl_error($ch);
        curl_close($ch);
    }

    /**
     * Post data.
     *
     * @param array $data
     *
     * @return $this
     */
    protected function buildBody(array $data): AbstractHttpRest
    {
        $this->requestBody = (string) json_encode($data);
        return $this;
    }

    /**
     * Curl options.
     *
     * @param resource $ch
     * @param array $headers
     *
     * @return void
     *
     * @throws Exception
     */
    protected function setCurlOpts(&$ch, array $headers): void
    {
        if (! is_string($this->url) || empty($this->url)) {
            throw new Exception('Invalid request URL', self::HTTP_CODE_500);
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, self::CURL_REQUEST_TIMOUT);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, self::CURL_REQUEST_MAX_REDIRECTS);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->verb);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        if (isset($this->token)) {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * @return array
     */
    protected function getResponseInfo(): ?array
    {
        return $this->responseInfo;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return $this
     */
    protected function setUrl(string $url): AbstractHttpRest
    {
        $this->url = $this->hostName . $url;
        return $this;
    }

    /**
     * Set the verb.
     *
     * @param string $verb
     *
     * @return $this
     */
    protected function setVerb(string $verb): AbstractHttpRest
    {
        $this->verb = $verb;
        return $this;
    }

    /**
     * @param mixed $response
     * @param string $method
     *
     * @return bool|int|stdClass|string|null
     */
    protected function processResponse($response, string $method)
    {
        if (strlen($this->curlError)) {
            $this->helper->logErrorMessage(__METHOD__, ': CURL ERROR: ' . $this->curlError);
            return false;
        }

        if (isset($response->status) && isset($response->detail)) {
            //For Profile merge request
            if ($response->status === self::HTTP_CODE_409) {
                return self::HTTP_CODE_409;
            }

            //Log error
            $this->helper->logErrorMessage($method, implode(" - ", (array) $response));

            //For getAccessToken api call
            if (strpos($method, '::getAccessToken') !== false) {
                return $response;
            }

            //All other error response handling
            return (in_array($response->status, self::ERROR_CODES_TO_RETRY)) ? false : (string) $response->detail;
        }

        return $response;
    }
}
