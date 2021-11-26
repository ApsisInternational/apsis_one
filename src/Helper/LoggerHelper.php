<?php

namespace Apsis\One\Helper;

use AbstractLogger;
use Apsis\One\Module\SetupInterface;
use FileLogger;
use Throwable;

class LoggerHelper extends FileLogger implements HelperInterface
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setFilename(_PS_ROOT_DIR_ . '/var/logs/' . _PS_ENV_ . '_apsis.log');
    }

    /**
     * {@inheritdoc}
     */
    public function logInfoMsg(string $message): void
    {
        $this->addLogEntryToFile(
            $this->addModuleVersionToMessage(
                str_replace(PHP_EOL, PHP_EOL . " -- ", $message), AbstractLogger::INFO
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function logDebugMsg(string $message, array $info): void
    {
        $info['Message/Method'] = $message;
        $this->addLogEntryToFile($this->getStringForLog($info, AbstractLogger::DEBUG));
    }

    /**
     * {@inheritdoc}
     */
    public function logErrorMsg(string $message, Throwable $e): void
    {
        $info = [
            'Method' => $message,
            'Exception' => $e->getMessage(),
            'Trace' => str_replace(PHP_EOL, PHP_EOL . "      ", PHP_EOL . $e->getTraceAsString())
        ];
        $this->addLogEntryToFile($this->getStringForLog($info, AbstractLogger::ERROR));
    }

    /**
     * @param array $info
     * @param int $level
     *
     * @return string
     */
    private function getStringForLog(array $info, int $level): string
    {
        return stripcslashes($this->addModuleVersionToMessage(json_encode($info, JSON_PRETTY_PRINT), $level));
    }

    /**
     * @param string $message
     * @param int $level
     *
     * @return string
     */
    private function addModuleVersionToMessage(string $message, int $level): string
    {
        return '*' . $this->level_value[$level] . '*' . "  v" . SetupInterface::MODULE_VERSION . "  " .
            date('Y/m/d - H:i:s') . ': ' . $message . "\r\n";
    }

    /**
     * @param string $message
     *
     * @return void
     */
    private function addLogEntryToFile(string $message): void
    {
        file_put_contents($this->getFilename(), $message, FILE_APPEND);
    }
}
