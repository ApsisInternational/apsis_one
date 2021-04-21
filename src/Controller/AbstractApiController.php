<?php

namespace Apsis\One\Controller;

use ModuleFrontController;
use WebserviceRequestCore;
use Apsis\One\Repository\ConfigurationRepository;
use Apsis_one;
use Validate;

abstract class AbstractApiController extends ModuleFrontController
{
    /**
     * @var Apsis_one
     */
    public $module;

    /**
     * @var ConfigurationRepository
     */
    protected $configurationRepository;

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
    protected $bodyParams = [];

    /**
     * AbstractApiController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->controller_type = 'module';
        $this->configurationRepository = $this->module->getService('apsis_one.repository.configuration');
    }

    public function init()
    {
        $this->validateHttpMethod();
        $this->authorize();

        if (in_array($this->validRequestMethod, ['POST', 'PATCH'])) {
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

    private function setBodyParams()
    {
        $body = file_get_contents('php://input');

        if (empty($body) || ! Validate::isJson($body)) {
            $this->exitWithResponse($this->generateResponse(400, [], 'Invalid payload.'));
        }

        $this->bodyParams = (array) json_decode($body);
    }

    private function validateBodyParams()
    {
        $bodyParams = array_diff($this->validBodyParams, array_keys($this->bodyParams));
        if (! empty($bodyParams)) {
            $msg = 'Incomplete payload. Missing ' . implode(', ', $bodyParams);
            $this->exitWithResponse($this->generateResponse(400, [], $msg));
        }

        foreach ($this->bodyParams as $param => $value) {
            if (empty($value) || ! Validate::isCleanHtml($value)) {
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
     * @return int[]
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