<?php

namespace Utility;

class FileLogger
{
    const EMERG  = 'EMERG';  // LOG_EMERG   system is unusable
    const ALERT  = 'ALERT';  // LOG_ALERT   action must be taken immediately
    const CRIT   = 'CRIT';   // LOG_CRIT    critical conditions
    const ERROR  = 'ERROR';  // LOG_ERR     error conditions
    const WARN   = 'WARN';   // LOG_WARNING warning conditions
    const NOTICE = 'NOTICE'; // LOG_NOTICE  normal, but significant, condition
    const INFO   = 'INFO';   // LOG_INFO    informational message
    const DEBUG  = 'DEBUG';  // LOG_DEBUG   debug-level message

    protected $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function emerg($message)
    {
        $this->write($message, self::EMERG);
    }

    public function alert($message)
    {
        $this->write($message, self::ALERT);
    }

    public function crit($message)
    {
        $this->write($message, self::CRIT);
    }

    public function error($message)
    {
        $this->write($message, self::ERROR);
    }

    public function warn($message)
    {
        $this->write($message, self::WARN);
    }

    public function notice($message)
    {
        $this->write($message, self::NOTICE);
    }

    public function info($message)
    {
        $this->write($message, self::INFO);
    }

    public function debug($message)
    {
        $this->write($message, self::DEBUG);
    }

    protected function write($message, $type)
    {
        $text = date('Y-m-d h:i:s ') . "[$type] " . $message . PHP_EOL;
        error_log($text, 3, $this->filename);
    }
}

//$logger = new FileLogger('zzz.log');
//$logger->info('Hello, start working');
//$logger->debug('working ended');
//$logger->notice('working ended');
