<?php

namespace Apsis\One\Controller;

use Apsis\One\Model\SchemaInterface;
use Apsis\One\Module\Configuration\Configs;
use Apsis\One\Helper\HelperInterface;
use Apsis_one;
use ModuleFrontController;
use WebserviceRequest;
use Validate;
use Tools;
use Throwable;

abstract class AbstractApiController extends ModuleFrontController implements ApiControllerInterface
{
    /**
     * @var Apsis_one
     */
    public $module;

    /**
     * @var Configs
     */
    protected $configs;

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
     * @var int|null
     */
    protected $groupId = null;

    /**
     * @var int|null
     */
    protected $shopId = null;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        try {
            parent::__construct();
            static::initClassProperties();

            $this->controller_type = 'module';
            $this->configs = $this->module->helper->getService(HelperInterface::SERVICE_MODULE_CONFIGS);
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }

    /**
     * @return void
     */
    abstract protected function initClassProperties(): void;

    /**
     * @return void
     */
    abstract protected function handleRequest(): void;

    /**
     * {@inheritdoc}
     */
    public function init(): void
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

            static::handleRequest();
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }

    /**
     * @return void
     */
    protected function validateHttpMethod(): void
    {
        try {
            if ($this->validRequestMethod !== $_SERVER['REQUEST_METHOD']) {
                $msg = $_SERVER['REQUEST_METHOD'] . ': method not allowed to this endpoint.';
                $this->module->helper->logDebugMsg(__METHOD__, ['info' => $msg]);

                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_405, [], $msg));
            }
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }

    /**
     * @return void
     */
    protected function authorize(): void
    {
        try {
            $headers = WebserviceRequest::getallheaders();
            if (empty($headers['Authorization']) ||
                $headers['Authorization'] !== $this->configs->getGlobalKey()
            ) {
                $msg = 'Invalid key for authorization header.';
                $this->module->helper->logDebugMsg(__METHOD__, ['info' => $msg]);

                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_401, [], $msg));
            }
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }

    /**
     * @return void
     */
    protected function validateModuleStatus(): void
    {
        try {
            if ($this->module->helper->isModuleEnabledForContext($this->groupId, $this->shopId) === false) {
                $msg = 'Module is disabled.';
                $this->module->helper->logDebugMsg(__METHOD__, ['info' => $msg]);

                $this->exitWithResponse(
                    $this->generateResponse(self::HTTP_CODE_403, [], $msg)
                );
            }
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }

    /**
     * @return void
     */
    protected function validateAndSetCompulsoryQueryParams(): void
    {
        try {
            foreach ($this->validQueryParams as $queryParam => $dataType) {
                if ($this->isOkToIgnoreParam($queryParam, self::PARAM_TYPE_QUERY)) {
                    continue;
                }

                if (! Tools::getIsset($queryParam)) {
                    $msg = "Missing query param: " . $queryParam;
                    $this->module->helper->logDebugMsg(__METHOD__, ['info' => $msg]);

                    $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_400, [], $msg));
                }

                $this->setQueryParam($queryParam, $dataType);
            }
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }

    /**
     * @param string $compulsoryParam
     * @param string $paramType
     *
     * @return bool
     */
    protected function isOkToIgnoreParam(string $compulsoryParam, string $paramType): bool
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
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }

        return false;
    }

    /**
     * @return void
     */
    protected function validateAndSetOptionalQueryParams(): void
    {
        try {
            foreach ($this->optionalQueryParams as $queryParam => $dataType) {
                if (Tools::getIsset($queryParam)) {
                    $this->setQueryParam($queryParam, $dataType);
                }
            }
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }

    /**
     * @param string $queryParam
     * @param string $dataType
     *
     * @return void
     */
    protected function setQueryParam(string $queryParam, string $dataType): void
    {
        try {
            $value = htmlspecialchars_decode(Tools::getValue($queryParam, false));
            if (! $this->isDataValid($value, $dataType)) {
                $msg = "Invalid value for query param: " . $queryParam;
                $this->module->helper->logDebugMsg(__METHOD__, ['info' => $msg]);

                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_400, [], $msg));
            }
            $this->queryParams[$queryParam] = Tools::safeOutput($value);

            if ($queryParam === self::QUERY_PARAM_CONTEXT_IDS) {
                $this->setContextIds($this->queryParams[$queryParam]);
            }
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }

    /**
     * @param string $contextIdsString
     *
     * @return void
     */
    protected function setContextIds(string $contextIdsString): void
    {
        try {
            $contextIds = explode(',', $contextIdsString);
            if (count($contextIds) === 2 && is_numeric($contextIds[0]) && is_numeric($contextIds[1])) {
                $this->groupId = (int) $contextIds[0];
                $this->shopId = (int) $contextIds[1];
            } else {
                $msg = 'Invalid context ids string.';
                $this->module->helper->logDebugMsg(__METHOD__, ['info' => $msg]);

                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_400, [], $msg));
            }
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }

    /**
     * @return void
     */
    protected function setBodyParams(): void
    {
        try {
            if ($this->isOkToIgnoreParam(self::PARAM_TYPE_BODY, self::PARAM_TYPE_BODY)) {
                $this->validBodyParams = [];
            } else {
                $body = file_get_contents('php://input');
                if (empty($body) || ! Validate::isJson($body)) {
                    $msg = 'Invalid payload.';
                    $this->module->helper->logDebugMsg(__METHOD__, ['info' => $msg]);

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
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }

    /**
     * @return void
     */
    protected function validateBodyParams(): void
    {
        try {
            $bodyParams = array_diff(array_keys($this->validBodyParams), array_keys($this->bodyParams));
            if (! empty($bodyParams)) {
                $msg = 'Incomplete payload. Missing body param ' . implode(', ', $bodyParams);
                $this->module->helper->logDebugMsg(__METHOD__, ['info' => $msg]);

                $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_400, [], $msg));
            }

            foreach ($this->bodyParams as $param => $value) {
                if (! $this->isDataValid($value, $this->validBodyParams[$param])) {
                    $msg = $param . ': is invalid.';
                    $this->module->helper->logDebugMsg(__METHOD__, ['info' => $msg]);

                    $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_400, [], $msg));
                }
            }
        } catch (Throwable $e) {
            $this->handleExcErr($e, __METHOD__);
        }
    }

    /**
     * @param mixed $data
     * @param string $type
     *
     * @return bool
     */
    protected function isDataValid($data, string $type): bool
    {
        $isValid = false;
        if (! empty($data)) {
            try {
                switch ($type) {
                    case self::DATA_TYPE_STRING:
                        $isValid = preg_match(SchemaInterface::VALID_GENERIC_NAME_PATTERN, $data);
                        break;
                    case self::DATA_TYPE_INT:
                        $isValid = is_numeric($data);
                        break;
                    case SchemaInterface::VALIDATE_FORMAT_URL_NOT_NULL:
                        $isValid = filter_var($data, FILTER_VALIDATE_URL);
                        break;
                }
            } catch (Throwable $e) {
                $this->handleExcErr($e, __METHOD__);
            }
        }
        return $isValid;
    }

    /**
     * @return void
     */
    protected function validateProfileSyncFeature(): void
    {
        if ($this->configs->getProfileSyncFlag($this->groupId, $this->shopId) === false) {
            $msg = 'Profile sync feature is disable for context.';
            $this->module->helper->logDebugMsg(__METHOD__, ['info' => $msg]);

            $this->exitWithResponse($this->generateResponse(self::HTTP_CODE_403, [], $msg));
        }
    }

    /**
     * @param array $response
     *
     * @return void
     */
    protected function exitWithResponse(array $response): void
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
    protected function generateResponse(int $httpCode, array $data = [], string $msg = ''): array
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
    protected function getStatusText(int $httpCode): string
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
     * @param Throwable $e
     * @param string $classMethodName
     *
     * @return void
     */
    protected function handleExcErr(Throwable $e, string $classMethodName): void
    {
        $this->module->helper->logErrorMsg($classMethodName, $e);
        $this->exitWithResponse(
            $this->generateResponse(AbstractApiController::HTTP_CODE_500, [], $e->getMessage())
        );
    }
}
