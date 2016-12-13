<?php

error_reporting(E_ALL);
ini_set("display_errors", "off");
register_shutdown_function("checkForFatal");
set_error_handler("errorHandler");
set_exception_handler("exceptionHandler");

date_default_timezone_set("America/Toronto");

#mb_internal_encoding('UTF-8');
#mb_http_output('UTF-8');
#mb_http_input('UTF-8');
#mb_language('uni');
#mb_regex_encoding('UTF-8');

const EOL = "\n";  // unix, PHP_EOL is os-specific

try {
    /**
     * Define some useful constants
     */
    define('BASE_DIR', dirname(__DIR__));
    define('APP_DIR', BASE_DIR . '/app');

    include 'trace.php';

    /**
     * include autoload earlier, so we can use class-constants in config.php
     */
    require_once __DIR__ . '/../vendor/autoload.php';

	/**
	 * Read the configuration
	 */
	$config = include APP_DIR . '/config/config.php';

	/**
	 * Read auto-loader
	 */
	include APP_DIR . '/config/loader.php';

	/**
	 * Read services
	 */
	include APP_DIR . '/config/services.php';

} catch (Exception $e) {
	echo $e->getMessage(), '<br>';
	echo nl2br(htmlentities($e->getTraceAsString()));
}

// error logger
//
function logError($msg)   { trigger_error($msg, E_USER_ERROR); }
function logWarning($msg) { trigger_error($msg, E_USER_WARNING); }
function logNotice($msg)  { trigger_error($msg, E_USER_NOTICE); }

function errorHandler($num, $str, $file, $line, $context = null)
{
    $types = [
        E_USER_ERROR => "Error: ",
        E_USER_WARNING => "Warning: ",
        E_USER_NOTICE => "Notice: ",

        E_ERROR => "ERROR: ",
        E_WARNING => "WARNING: ",
        E_NOTICE => "NOTICE: ",
    ];

    $type = "ERROR: ";

    if (isset($types[$num])) {
        $type = $types[$num];
    }

    exceptionHandler(new ErrorException($type.$str, 0, $num, $file, $line));
}

function exceptionHandler(Exception $e)
{
    $today = date('Y-m-d');

    $filename = APP_DIR . "/logs/exception-$today.log";

    $message  = date('H:i:s ').$e->getMessage() . EOL;
    $message .= "\t";
    $message .= str_replace('\\', '/', $e->getFile()).':'.$e->getLine().EOL;

    $message .= "Backtrace:\n";
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    foreach ($backtrace as $trace) {
        if (isset($trace['file'])) {
            $file = str_replace('\\', '/', $trace['file']);
            $line = $trace['line'];
            $message .= "\t$file:$line\n";
        } else {
            $message .= print_r($trace, true);
        }
    }
    $message .= EOL;

#   echo $message;

#   file_put_contents($filename, $message, FILE_APPEND);
    error_log($message, 3, $filename);
}

function checkForFatal()
{
    $error = error_get_last();
    if ($error["type"] == E_ERROR) {
        errorHandler($error["type"], $error["message"], $error["file"], $error["line"]);
    }
}
