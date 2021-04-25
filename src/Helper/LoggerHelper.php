<?php

namespace Apsis\One\Helper;

use PrestaShopLogger;
use FileLogger;

class LoggerHelper
{
    const LOG_FILE_NAME = _PS_ROOT_DIR_ . '/var/logs/apsis.log';

    /**
     * @var FileLogger
     */
    protected $fileLogger;

    /**
     * LoggerHelper constructor.
     */
    public function __construct()
    {
        $logger = new FileLogger();
        $logger->setFilename(self::LOG_FILE_NAME);
        $this->fileLogger = $logger;
    }

    /**
     * @param string $message
     * @param int $severity
     */
    public function addLogToDatabase(string $message, int $severity = 1)
    {
        PrestaShopLogger::addLog($message, $severity);
    }

    /**
     * Log a debug message.
     *
     * @param string message
     */
    public function logDebugToFile(string $message)
    {
        $this->fileLogger->logDebug($message);
    }

    /**
     * Log an info message.
     *
     * @param string message
     */
    public function logInfoToFile(string $message)
    {
        $this->fileLogger->logInfo($message);
    }

    /**
     * Log a warning message.
     *
     * @param string message
     */
    public function logWarningToFile(string $message)
    {
        $this->fileLogger->logWarning($message);
    }

    /**
     * Log an error message.
     *
     * @param string $classMethodName
     * @param string $text
     * @param string $trace
     */
    public function logErrorToFile(string $classMethodName, string $text, string $trace = '')
    {
        $this->fileLogger->logError($this->getStringForLog($classMethodName, $text, $trace));
    }

    /**
     * @param string $functionName
     * @param string $text
     * @param string $trace
     *
     * @return string
     */
    private function getStringForLog(string $functionName, string $text, string $trace)
    {
        $string = ' - Class & Method: ' . $functionName . ' - Text: ' . $text;
        return strlen($trace) ? $string . PHP_EOL . $trace : $string;
    }
}
