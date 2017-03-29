<?php

namespace Service;

use Phalcon\Di\Injectable;

class LoggerService extends Injectable
{
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';

    protected $filename;
    protected $firstCall = [];

    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * System is unusable.
     */
    public function emergency($message, array $context = array())
    {
        $this->log(self::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     */
    public function alert($message, array $context = array())
    {
        $this->log(self::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     */
    public function critical($message, array $context = array())
    {
        $this->log(self::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     */
    public function error($message, array $context = array())
    {
        $this->log(self::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     */
    public function warning($message, array $context = array())
    {
        $this->log(self::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     */
    public function notice($message, array $context = array())
    {
        $this->log(self::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     */
    public function info($message, array $context = array())
    {
        $this->log(self::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     */
    public function debug($message, array $context = array())
    {
        $this->log(self::DEBUG, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     */
    public function log($level, $message, array $context = array())
    {
        // filename
        $today = date('Y-m-d');

        $fname = $this->filename ? pathinfo($this->filename, PATHINFO_FILENAME) : $level;

        $filename = APP_DIR . "/logs/$fname-$today.log";

        // message
        $msg = '';
        if (!isset($this->firstCall[$level])) {
            $msg .= "\n";
            $this->firstCall[$level] = false;
        }

        $msg .= date('H:i:s ');

        if ($this->filename) {
            $msg .= '['. strtoupper($level) .'] ';
        }

        $msg .= $message."\n";

        if ($context) {
            $msg .= var_export($context, true)."\n";
        }

        error_log($msg, 3, $filename);
    }
}
