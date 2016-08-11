<?php

use Phalcon\Di\FactoryDefault\Cli as CliDI,
    Phalcon\Cli\Console as ConsoleApp;

define('VERSION', '1.0.0');

// Using the CLI factory default services container
$di = new CliDI();

// Define path to application directory
defined('APP_PATH') || define('APP_PATH', realpath(dirname(__FILE__)));

/**
 * Register the autoloader and tell it to register the tasks directory
 */
$loader = new \Phalcon\Loader();
$loader->registerDirs(
    array(
        APP_PATH . '/tasks'
    )
);
$loader->register();

// Load the configuration file (if any)
if (is_readable(APP_PATH . '/config/config.php')) {
    $config = include APP_PATH . '/config/config.php';
    //$di->set('config', $config);
    $di->set('config', new \Phalcon\Config($config));
}

$di->setShared('db', function() use ($di) {
    $type = strtolower($di->get('config')->database->adapter);

    $creds = array(
        'host'      => $di->get('config')->database->host,
        'username'  => $di->get('config')->database->username,
        'password'  => $di->get('config')->database->password,
        'dbname'    => $di->get('config')->database->name
    );

    if ($type == 'mysql') {
        $connection =  new \Phalcon\Db\Adapter\Pdo\Mysql($creds);
    } else if ($type == 'postgres') {
        $connection =  new \Phalcon\Db\Adapter\Pdo\Postgesql($creds);
    } else if ($type == 'sqlite') {
        $connection =  new \Phalcon\Db\Adapter\Pdo\Sqlite($creds);
    } else {
        throw new Exception('Bad Database Adapter');
    }

    return $connection;
});

// Create a console application
$console = new ConsoleApp();
$console->setDI($di);

$di->setShared('console', $console);

/**
 * Process the console arguments
 */
$arguments = array();
foreach ($argv as $k => $arg) {
    if ($k == 1) {
        $arguments['task'] = $arg;
    } elseif ($k == 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

// Define global constants for the current task and action
define('CURRENT_TASK',   (isset($argv[1]) ? $argv[1] : null));
define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

try {
    // Handle incoming arguments
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
    exit(255);
}
