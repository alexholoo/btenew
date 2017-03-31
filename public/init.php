<?php

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
    define('APP_DIR',  BASE_DIR . '/app');
    define('IS_PROD',  gethostname() == 'BTELENOVO');
    define('IS_PHP64', PHP_INT_SIZE == 8);
    define('IS_PHP32', PHP_INT_SIZE == 4);

    include 'trace.php';
    include 'error.php';

    /**
     * include classes that almost every job needs
     */
    include '../job/Filenames.php';
    include '../job/classes/Job.php';

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
