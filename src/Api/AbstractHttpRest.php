<?php

namespace Apsis\One\Api;

use Apsis\One\Controller\ApiControllerInterface;
use Apsis\One\Helper\HelperInterface;
use Apsis\One\Module\Configuration\Configs;
use Apsis\One\Module\SetupInterface;
use Exception;
use Throwable;
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
    protected $token;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var null|int
     */
    protected $idShopGroup;

    /**
     * @var null|int
     */
    protected $idShop;

    /**
     * @var Configs
     */
    protected $configs;

    /**
     * @var string
     */
    protected $verb;

    /**
     * @var string
     */
    protected $requestBody;

    /**
     * @var null|stdClass
     */
    protected $responseBody;

    /**
     * @var array
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
     *
     * @throws Throwable
     */
    public function __construct(HelperInterface $helper, string $host)
    {
        if (empty($host)) {
            throw new Exception('Host cannot be empty', self::HTTP_CODE_500);
        }

        if (function_exists('curl_init') === false) {
            throw new Exception('CURL is not enabled', self::HTTP_CODE_500);
        }

        $this->helper = $helper;
        $this->hostName = $host;
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        $this->responseBody = null;
        $this->responseInfo = [];
        $this->curlError = '';
    }

    /**
     * @return null|stdClass
     */
    protected function execute(): ?stdClass
    {
        $this->init();

        if (strpos($this->url, '/oauth/token') === false && empty($this->token)) {
            $this->curlError = 'Token cannot be empty for endpoint URL: ' . $this->url;
            return $this->processResponse($this->responseBody, __METHOD__);
        }

        $ch = curl_init();
        if ($ch === false) {
            $this->curlError = 'Unable to initiate cURL resource';
            return $this->processResponse($this->responseBody, __METHOD__);
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
                    $this->curlError = 'Current verb (' . $this->verb . ') is an invalid REST verb.';
                    $this->helper->logDebugMsg(__METHOD__, ['Message' => 'invalid REST verb', 'Verb' => $this->verb]);
                    curl_close($ch);
            }
        } catch (Throwable $e) {
            curl_close($ch);
            $this->curlError = $e->getMessage();
            $this->helper->logErrorMsg(__METHOD__, $e);
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
     * @throws Throwable
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
     * @throws Throwable
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
     * @throws Throwable
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
     * @throws Throwable
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
     * @throws Throwable
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
     * @throws Throwable
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
     * @throws Throwable
     */
    protected function doExecute(&$ch, array $headers): void
    {
        $this->setCurlOpts($ch, $headers);
        $this->responseBody = json_decode(curl_exec($ch));
        $this->curlError = curl_error($ch);
        if (empty($this->curlError)) {
            $this->responseInfo = curl_getinfo($ch);
        }
        curl_close($ch);
    }

    /**
     * Curl options.
     *
     * @param resource $ch
     * @param array $headers
     *
     * @return void
     *
     * @throws Throwable
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
        if (isset($this->token)) {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * Post data.
     *
     * @param array|null $data
     *
     * @return $this
     */
    protected function buildBody(?array $data = null): AbstractHttpRest
    {
        $this->requestBody = (string) json_encode($data);
        return $this;
    }

    /**
     * @return $this
     */
    protected function buildBodyForGetAccessTokenCall(): AbstractHttpRest
    {
        return $this->buildBody([
            'grant_type' => 'client_credentials',
            SetupInterface::INSTALLATION_CONFIG_CLIENT_ID => $this->clientId,
            SetupInterface::INSTALLATION_CONFIG_CLIENT_SECRET => $this->clientSecret
        ]);
    }

    /**
     * @param string $token
     *
     * @return $this
     */
    public function setToken(string $token): AbstractHttpRest
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @param string $clientId
     * @param string $clientSecret
     *
     * @return $this
     */
    public function setClientCredentials(string $clientId, string $clientSecret): AbstractHttpRest
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        return $this;
    }

    /**
     * @param Configs $configs
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return $this
     */
    public function setConfigScope(Configs $configs, ?int $idShopGroup, ?int $idShop): AbstractHttpRest
    {
        $this->configs = $configs;
        $this->idShopGroup = $idShopGroup;
        $this->idShop = $idShop;
        return $this;
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
     * @return mixed
     */
    protected function processResponse($response, string $method)
    {
        if (strlen($this->curlError)) {
            $this->helper->logDebugMsg(__METHOD__, ['CURL ERROR' => $this->curlError]);
            return false;
        }

        if ((bool) getenv('APSIS_DEVELOPER') && ! empty($this->responseInfo)) {
            $info = [
                'Method' => $method,
                'Request time in seconds' => $this->responseInfo['total_time'],
                'Endpoint URL' => $this->responseInfo['url'],
                'Http code' => $this->responseInfo['http_code'],
                'Response' => $response
            ];
            $this->helper->logDebugMsg(__METHOD__, ['CURL Transfer' => $info]);
        }

        if (isset($response->status) && isset($response->detail)) {
            //Log error
            $this->helper->logDebugMsg($method, (array) $response);

            if (strpos($method, '::getAccessToken') !== false) {
                // Return as it is
                return $response;
            } elseif (in_array($response->status, self::HTTP_CODES_FORCE_GENERATE_TOKEN)) {
                // Client factory will automatically generate new one. If not then will disable automatically.
                $this->configs->clearTokenConfigs($this->idShopGroup, $this->idShop);
                return false;
            } elseif ($response->status === self::HTTP_CODE_409) {
                //For Profile merge request
                return self::HTTP_CODE_409;
            }

            //All other error response handling
            return (in_array($response->status, self::HTTP_ERROR_CODES_TO_RETRY)) ? false : (string) $response->detail;
        }

        return $response;
    }
}
