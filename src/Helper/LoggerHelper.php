<?php

namespace Apsis\One\Helper;

use AbstractLogger;
use Apsis\One\Module\SetupInterface;
use FileLogger;

class LoggerHelper extends FileLogger implements HelperInterface
{
    /**
     * LoggerHelper constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setFilename(_PS_ROOT_DIR_ . '/var/logs/' . _PS_ENV_ . '_apsis.log');
    }

    /**
     * @inheritdoc
     */
    public function addLogEntryToFile(string $message, int $level = AbstractLogger::INFO): void
    {
        $formatted_message = '*' . $this->level_value[$level] . '* ' . "\tv" . SetupInterface::MODULE_VERSION . "\t" .
            date('Y/m/d - H:i:s') . ': ' . $message . "\r\n";

        file_put_contents($this->getFilename(), $formatted_message, FILE_APPEND);
    }

    /**
     * @inheritdoc
     */
    public function logMsg($message, int $level = AbstractLogger::INFO): void
    {
        if (! is_string($message) ) {
            $message = print_r($message, true);
        }

        $this->addLogEntryToFile($message, $level);
    }

    /**
     * @inheritdoc
     */
    public function logErrorMessage(string $classMethodName, string $text, string $trace = ''): void
    {
        $this->logMsg(['Method' => $classMethodName, 'Message' => $text, 'Trace' => $trace], AbstractLogger::ERROR);
    }
}
