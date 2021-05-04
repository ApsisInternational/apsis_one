<?php

namespace Apsis\One\Controller;

use Apsis\One\Helper\LoggerHelper;
use Apsis\One\Repository\ConfigurationRepository;
use Apsis_one;
use ModuleFrontController;
use WebserviceRequestCore;
use Validate;
use Tools;
use Exception;

abstract class AbstractApiController extends ModuleFrontController
{
    const PARAM_TYPE_QUERY = 'query';
    const PARAM_TYPE_BODY = 'body';

    /** QUERY PARAMS */
    const QUERY_PARAM_CONTEXT_IDS = 'context_ids';

    /** DATA TYPES */
    const DATA_TYPE_STRING = 'string';
    const DATA_TYPE_INT = 'int';
    const DATA_TYPE_URL = 'url';

    /** HTTP METHODS */
    const HTTP_GET = 'GET';
    const HTTP_POST = 'POST';
    const HTTP_PATCH = 'PATCH';

    /** HTTP Codes  */
    const HTTP_CODE_200 = 200;
    const HTTP_CODE_204 = 204;
    const HTTP_CODE_400 = 400;
    const HTTP_CODE_401 = 401;
    const HTTP_CODE_403 = 403;
    const HTTP_CODE_404 = 404;
    const HTTP_CODE_405 = 405;
    const HTTP_CODE_500 = 500;

    const REQUEST_BODY_FOR_HTTP_METHOD = [self::HTTP_POST, self::HTTP_PATCH];

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
    protected $optionalQueryParamIgnoreRelations = [];

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
    protected $groupId = null;

    /**
     * @var int
     */
    protected $shopId = null;

    /**
     * AbstractApiController constructor.
     */
    public function __construct()
    {
        try {
            parent::__construct();

            $this->controller_type = 'module';
            $this->configurationRepository = $this->module->getService('apsis_one.repository.configuration');
            $this->loggerHelper = $this->module->getService('apsis_one.helper.logger');
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    abstract protected function handleRequest();

    public function init()
    {
        try {
            //Check if http method is allowed or not
            $this->validateHttpMethod();

            //Check if authorized to make the call
            $this->authorize();

            //Check|set if optional query params are valid
            $this->validateAndSetOptionalQueryParams();

            //Check|set if compulsory query params are valid
            $this->validateAndSetCompulsoryQueryParams();

            //Check if module is enabled
            $this->validateModuleStatus();

            //Check|set body params if http method allows it
            if (in_array($this->validRequestMethod, self::REQUEST_BODY_FOR_HTTP_METHOD)) {
                $this->setBodyParams();
                $this->validateBodyParams();
            }
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    private function validateHttpMethod()
    {
        try {
            if ($this->validRequestMethod !== $_SERVER['REQUEST_METHOD']) {
                $msg = $_SERVER['REQUEST_METHOD'] . ': method not allowed to this endpoint.';
                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_405, [], $msg));
            }
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    private function authorize()
    {
        try {
            $headers = WebserviceRequestCore::getallheaders();
            if (empty($headers['Authorization']) ||
                $headers['Authorization'] !== $this->configurationRepository->getGlobalKey()
            ) {
                $msg = 'Invalid key for authorization header.';
                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_401, [], $msg));
            }
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    private function validateModuleStatus()
    {
        try {
            if ($this->module->isModuleEnabledForContext($this->groupId, $this->shopId) === false) {
                $this->exitWithResponse(
                    $this->generateResponse(self::HTTP_CODE_403, [], 'Module is disabled.')
                );
            }
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    private function validateAndSetCompulsoryQueryParams()
    {
        try {
            foreach ($this->validQueryParams as $queryParam => $dataType) {
                if ($this->isOkToIgnoreParam($queryParam, self::PARAM_TYPE_QUERY)) {
                    continue;
                }

                if (! Tools::getIsset($queryParam)) {
                    $msg = "Missing query param: " . $queryParam;
                    $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_400, [], $msg));
                }

                $this->setQueryParam($queryParam, $dataType);
            }
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    /**
     * @param string $compulsoryParam
     * @param string $paramType
     *
     * @return bool
     */
    private function isOkToIgnoreParam(string $compulsoryParam, string $paramType)
    {
        try {
            foreach ($this->optionalQueryParamIgnoreRelations as $param => $typeList) {
                if (key_exists($param, $this->queryParams) &&
                    isset($typeList[$paramType]) &&
                    ! empty($list = $typeList[$paramType]) &&
                    in_array($compulsoryParam, $list)
                ) {
                    return true;
                }
            }
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }

        return false;
    }

    private function validateAndSetOptionalQueryParams()
    {
        try {
            foreach ($this->optionalQueryParams as $queryParam => $dataType) {
                if (Tools::getIsset($queryParam)) {
                    $this->setQueryParam($queryParam, $dataType);
                }
            }
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    /**
     * @param string $queryParam
     * @param string $dataType
     */
    private function setQueryParam(string $queryParam, string $dataType)
    {
        try {
            $value = Tools::getValue($queryParam, false);
            if (! $this->isDataValid($value, $dataType)) {
                $msg = "Invalid value for query param: " . $queryParam;
                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_400, [], $msg));
            }
            $this->queryParams[$queryParam] = Tools::safeOutput($value);

            if ($queryParam === self::QUERY_PARAM_CONTEXT_IDS) {
                $this->setContextIds($this->queryParams[$queryParam]);
            }
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    private function setContextIds(string $contextIdsString)
    {
        try {
            $contextIds = explode(',', $contextIdsString);
            if (count($contextIds) === 2 && is_numeric($contextIds[0]) && is_numeric($contextIds[1])) {
                $this->groupId = (int) $contextIds[0];
                $this->shopId = (int) $contextIds[1];
            } else {
                $msg = 'Invalid context ids string.';
                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_400, [], $msg));
            }
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    private function setBodyParams()
    {
        try {
            if ($this->isOkToIgnoreParam(self::PARAM_TYPE_BODY, self::PARAM_TYPE_BODY)) {
                $this->validBodyParams = [];
            } else {
                $body = file_get_contents('php://input');
                if (empty($body) || ! Validate::isJson($body)) {
                    $msg = 'Invalid payload.';
                    $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_400, [], $msg));
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
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
    }

    private function validateBodyParams()
    {
        try {
            $bodyParams = array_diff(array_keys($this->validBodyParams), array_keys($this->bodyParams));
            if (! empty($bodyParams)) {
                $msg = 'Incomplete payload. Missing body param ' . implode(', ', $bodyParams);
                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_400, [], $msg));
            }

            foreach ($this->bodyParams as $param => $value) {
                if (! $this->isDataValid($value, $this->validBodyParams[$param])) {
                    $msg1 = $param . ': is invalid.';
                    $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_400, [], $msg1));
                }
            }
        } catch (Exception $e) {
            $this->handleException($e, __METHOD__);
        }
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
        if (! empty($data)) {
            try {
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
            } catch (Exception $e) {
                $this->handleException($e, __METHOD__);
            }
        }
        return $isValid;
    }

    protected function validateProfileSyncFeature()
    {
        if ($this->configurationRepository->getProfileSyncFlag($this->groupId, $this->shopId) === false) {
            $msg = 'Profile sync feature is disable for context.';
            $this->exitWithResponse($this->generateResponse(AbstractApiController::HTTP_CODE_403, [], $msg));
        }
    }

    /**
     * @param array $response
     */
    protected function exitWithResponse(array $response)
    {
        $response['httpCode'] = isset($response['httpCode']) ? (int) $response['httpCode'] : self::HTTP_CODE_204;
        $httpStatusText = $this->getStatusText($response['httpCode']);

        if ($response['httpCode'] === self::HTTP_CODE_204) {
            $response = [];
        }

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
            $response['data'] = $data;
        }
        if (strlen($msg)) {
            $response['message'] = $msg;
        }
        return $response;
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
            case self::HTTP_CODE_200:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' ' . self::HTTP_CODE_200 . ' OK';

                break;
            case self::HTTP_CODE_204:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' ' . self::HTTP_CODE_204 . ' No Content';

                break;
            case self::HTTP_CODE_400:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' ' . self::HTTP_CODE_400 . ' Bad Request';

                break;
            case self::HTTP_CODE_401:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' ' . self::HTTP_CODE_401 . ' Unauthorized';

                break;
            case self::HTTP_CODE_403:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' ' . self::HTTP_CODE_403 . ' Forbidden';

                break;
            case self::HTTP_CODE_404:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' ' . self::HTTP_CODE_404 . ' Not Found';

                break;
            case self::HTTP_CODE_405:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' ' . self::HTTP_CODE_405 . ' Method Not Allowed';

                break;
            case self::HTTP_CODE_500:
                $statusText = $_SERVER['SERVER_PROTOCOL'] . ' ' . self::HTTP_CODE_500 . ' Internal Server Error';

                break;
        }
        return $statusText;
    }

    /**
     * @param Exception $e
     * @param string $classMethodName
     */
    protected function handleException(Exception $e, string $classMethodName)
    {
        $this->loggerHelper->logErrorToFile($classMethodName, $e->getMessage(), $e->getTraceAsString());
        $this->exitWithResponse(
            $this->generateResponse(AbstractApiController::HTTP_CODE_500, [], $e->getMessage())
        );
    }
}