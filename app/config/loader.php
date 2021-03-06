<?php

use Phalcon\Loader;

$loader = new Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerNamespaces([
    'App\Models'      => $config->application->modelsDir,
    'App\Controllers' => $config->application->controllersDir,
    'App\Forms'       => $config->application->formsDir,
    'App\Library'     => $config->application->libraryDir,
    'App\Plugins'     => $config->application->pluginsDir,
]);

$loader->register();

// Use composer autoloader to load vendor classes
require_once __DIR__ . '/../../vendor/autoload.php';
