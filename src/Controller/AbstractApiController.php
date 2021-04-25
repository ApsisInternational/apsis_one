<?php

namespace Apsis\One\Controller;

use Apsis\One\Helper\LoggerHelper;
use ModuleFrontController;
use WebserviceRequestCore;
use Apsis\One\Repository\ConfigurationRepository;
use Apsis_one;
use Validate;
use Tools;

abstract class AbstractApiController extends ModuleFrontController
{
    /** QUERY PARAMS */
    const QUERY_PARAM_CONTEXT_IDS = 'context_ids';
    const QUERY_PARAM_RESET = 'reset';

    /** DATA TYPES */
    const DATA_TYPE_STRING = 'string';
    const DATA_TYPE_INT = 'int';
    const DATA_TYPE_URL = 'url';

    /** HTTP METHODS */
    const HTTP_GET = 'GET';
    const HTTP_POST = 'POST';
    const HTTP_PUT = 'PUT';
    const HTTP_PATCH = 'PATCH';
    const HTTP_DELETE = 'DELETE';

    const REQUEST_BODY_FOR_HTTP_METHOD = [self::HTTP_POST, self::HTTP_PATCH, self::HTTP_PUT];

    /**
     * @var Apsis_one
     */
    public $module;

    /**
     * @var ConfigurationRepository
     */
    protected $configurationRepository;

    /**
     * @var LoggerHelper
     */
    protected $loggerHelper;

    /**
     * @var string
     */
    protected $validRequestMethod;

    /**
     * @var array
     */
    protected $validBodyParams = [];

    /**
     * @var array
     */
    protected $validQueryParams = [];

    /**
     * @var array
     */
    protected $optionalQueryParams = [];

    /**
     * @var array
     */
    protected $bodyParams = [];

    /**
     * @var array
     */
    protected $queryParams = [];

    /**
     * @var int
     */
    protected $groupId;

    /**
     * @var int
     */
    protected $shopId;

    /**
     * AbstractApiController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->controller_type = 'module';
        $this->configurationRepository = $this->module->getService('apsis_one.repository.configuration');
        $this->loggerHelper = $this->module->getService('apsis_one.helper.logger');
    }

    public function init()
    {
        $this->validateHttpMethod();
        $this->authorize();
        $this->validateAndSetQueryParams();
        $this->validateAndSetOptionalQueryParams();

        if (isset($this->queryParams[self::QUERY_PARAM_CONTEXT_IDS])) {
            $this->setContextIds();
        }

        $this->checkForResetParam();

        if (in_array($this->validRequestMethod, self::REQUEST_BODY_FOR_HTTP_METHOD)) {
            $this->setBodyParams();
            $this->validateBodyParams();
        }
    }

    private function validateHttpMethod()
    {
        if ($this->validRequestMethod !== $_SERVER['REQUEST_METHOD']) {
            $msg = $_SERVER['REQUEST_METHOD'] . ': method not allowed to this endpoint.';
            $this->exitWithResponse($this->generateResponse(405, [], $msg));
        }
    }

    private function authorize()
    {
        $headers = WebserviceRequestCore::getallheaders();
        if (empty($headers['Authorization']) ||
            $headers['Authorization'] !== $this->configurationRepository->getGlobalKey()
        ) {
            $this->exitWithResponse($this->generateResponse(401, [], 'Invalid key.'));
        }
    }

    private function validateAndSetQueryParams()
    {
        foreach ($this->validQueryParams as $queryParam => $dataType) {
            if (! Tools::getIsset($queryParam)) {
                $msg = "Missing query param: " . $queryParam;
                $this->exitWithResponse($this->generateResponse(400, [], $msg));
            }

            $this->setQueryParam($queryParam, $dataType);
        }
    }

    private function validateAndSetOptionalQueryParams()
    {
        foreach ($this->optionalQueryParams as $queryParam => $dataType) {
            if (Tools::getIsset($queryParam)) {
                $this->setQueryParam($queryParam, $dataType);
            }
        }
    }

    /**
     * @param string $queryParam
     * @param string $dataType
     */
    private function setQueryParam(string $queryParam, string $dataType)
    {
        $value = Tools::getValue($queryParam, false);
        if (! $this->isDataValid($value, $dataType)) {
            $msg = "Invalid value for query param: " . $queryParam;
            $this->exitWithResponse($this->generateResponse(400, [], $msg));
        }
        $this->queryParams[$queryParam] = Tools::safeOutput($value);
    }

    private function setContextIds()
    {
        $contextIds = explode(',', $this->queryParams[self::QUERY_PARAM_CONTEXT_IDS]);
        if (count($contextIds) === 2 && is_numeric($contextIds[0]) && is_numeric($contextIds[1])) {
            $this->groupId = (int) $contextIds[0];
            $this->shopId = (int) $contextIds[1];
        } else {
            $this->exitWithResponse($this->generateResponse(400, [], 'Invalid context ids.'));
        }
    }

    private function checkForResetParam()
    {
        if (get_class($this) === "apsis_OneApiInstallationConfigModuleFrontController" &&
            ! empty($this->queryParams[self::QUERY_PARAM_RESET])
        ) {
            //@todo also reset events and profiles
            $context = $this->configurationRepository->getContextForSavingConfig(
                ConfigurationRepository::CONFIG_KEY_INSTALLATION_CONFIGS,
                $this->groupId,
                $this->shopId
            );
            $this->configurationRepository->disableFeaturesAndDeleteConfig($this->groupId, $this->shopId);
            $this->configurationRepository->saveInstallationConfigs([], $context['idShopGroup'], $context['idShop']);
            $this->exitWithResponse($this->generateResponse(204));
        }
    }

    private function setBodyParams()
    {
        $body = file_get_contents('php://input');

        if (empty($body) || ! Validate::isJson($body)) {
            $this->exitWithResponse($this->generateResponse(400, [], 'Invalid payload.'));
        }

        $params = (array) json_decode($body);
        foreach ($params as $key => $value) {
            if (! isset($this->validBodyParams[$key])) {
                unset($params[$key]);
            } else {
                $params[$key] = Tools::safeOutput($value);
            }
        }

        $this->bodyParams = $params;
    }

    private function validateBodyParams()
    {
        $bodyParams = array_diff(array_keys($this->validBodyParams), array_keys($this->bodyParams));
        if (! empty($bodyParams)) {
            $msg = 'Incomplete payload. Missing ' . implode(', ', $bodyParams);
            $this->exitWithResponse($this->generateResponse(400, [], $msg));
        }

        foreach ($this->bodyParams as $param => $value) {
            if (! $this->isDataValid($value, $this->validBodyParams[$param])) {
                $this->exitWithResponse($this->generateResponse(400, [], $param . ': is invalid.'));
            }
        }
    }

    /**
     * @param array $response
     */
    protected function exitWithResponse(array $response)
    {
        $response['httpCode'] = $httpCode = isset($response['httpCode']) ? (int) $response['httpCode'] : 200;
        $httpStatusText = $this->getStatusText($httpCode);

        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: application/json;charset=utf-8');
        header($httpStatusText);

        echo json_encode($response, JSON_UNESCAPED_SLASHES);

        exit;
    }

    /**
     * @param int $httpCode
     * @param array $data
     * @param string $msg
     *
     * @return array
     */
    protected function generateResponse(int $httpCode, array $data = [], string $msg = '')
    {
        $response = ['httpCode' => $httpCode];
        if (! empty($data)) {
            $response['items'] = $data;
        }
        if (strlen($msg)) {
            $response['message'] = $msg;
        }
        return $response;
    }

    /**
     * @param mixed $data
     * @param string $type
     *
     * @return bool
     */
    private function isDataValid($data, string $type)
    {
        $isValid = false;

        if (empty($data)) {
            return $isValid;
        }

        switch ($type) {
            case self::DATA_TYPE_STRING:
                $isValid = preg_match('/^[a-zA-Z0-9.,_-]+$/', $data);
                break;
            case self::DATA_TYPE_INT:
                $isValid = is_numeric($data);
                break;
            case self::DATA_TYPE_URL:
                $isValid = filter_var($data, FILTER_VALIDATE_URL);
                break;
        }

        return $isValid;
    }

    /**
     * @param int $httpCode
     *
     * @return string
     */
    private function getStatusText(int $httpCode)
    {
        $statusText = '';
        switch ($httpCode) {
            case 200:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' 200 OK';

                break;
            case 201:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' 201 Created';

                break;
            case 204:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' 204 No Content';

                break;
            case 304:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified';

                break;
            case 400:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request';

                break;
            case 401:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized';

                break;
            case 403:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden';

                break;
            case 404:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found';

                break;
            case 405:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed';

                break;
            case 500:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error';

                break;
            case 501:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' 501 Not Implemented';

                break;
            case 503:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable';

                break;
        }
        return $statusText;
    }
}