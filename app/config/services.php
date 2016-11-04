<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\View;
use Phalcon\Crypt;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Files as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Logger\Formatter\Line as FormatterLine;
use Phalcon\Logger;
use Phalcon\Events\Manager as EventsManager;

use Service\ProductService;
use Service\PricelistService;
use Service\InventoryServi;
use Service\OrderService;
use Service\PurchaseService;
use Service\ShipmentService;
use Service\ConfigService;
use Service\AmazonService;
use Service\EbayService;
use Service\NeweggService;
use Service\RakutenService;
use Service\PriceAvailService;

use App\Library\Auth\Auth;
use App\Library\Acl\Acl;
use App\Library\Mail\Mail;

use App\Plugins\NotFoundPlugin;
use UserPlugin\Plugin\Security as SecurityPlugin;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * Register the global configuration as config
 */
$di->set('config', $config);

$evtMgr = new EventsManager();
$evtMgr->enablePriorities(true);

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);
    return $url;
}, true);

/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines(array(
        '.volt' => function ($view, $di) use ($config) {

            $volt = new VoltEngine($view, $di);

            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir . 'volt/',
                'compiledSeparator' => '_',
                'compiledPath' => function($templatePath) use ($config) {
                    return $config->application->cacheDir . 'volt/' . md5($templatePath) . '.php';
                },
            ));

            return $volt;
        }
    ));

    return $view;
}, true);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {
    // db logger deleted, see git log

    $connection = new DbAdapter(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        'options' => [ \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8' ],
        'charset' =>'utf8'
    ));

    return $connection;
});

Model::setup(['notNullValidations' => false]);

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () use ($config) {
    return new MetaDataAdapter(array(
        'metaDataDir' => $config->application->cacheDir . 'metaData/'
    ));
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
    $session = new SessionAdapter();
    $session->start();
    return $session;
});

/**
 * Crypt service
 */
$di->set('crypt', function () use ($config) {
    $crypt = new Crypt();
    $crypt->setKey($config->application->cryptSalt);
    return $crypt;
});

/**
 * Dispatcher use a default namespace
 */
$di->set('dispatcher', function () use ($evtMgr) {
    $evtMgr->attach('dispatch:beforeException', new NotFoundPlugin);
#   $evtMgr->attach('dispatch:beforeDispatch',  new SecurityPlugin($di));

    $dispatcher = new Dispatcher();
    $dispatcher->setDefaultNamespace('App\Controllers');
    $dispatcher->setEventsManager($evtMgr);

    return $dispatcher;
});

/**
 * Loading routes from the routes.php file
 */
$di->set('router', function () {
    return require __DIR__ . '/routes.php';
});

/**
 * Flash service with custom CSS classes
 */
$di->set('flash', function () {
    return new Flash(array(
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ));
});

/**
 * Custom authentication component
 */
$di->set('auth', function () {
    return new Auth();
});

/**
 * Mail service uses AmazonSES
 */
$di->set('mail', function () {
    return new Mail();
});

/**
 * Access Control List
 */
$di->set('acl', function () {
    return new Acl();
});

/**
 * Logger service
 */
$di->set('logger', function ($filename = null, $format = null) use ($config) {
    $today    = date('Y-m-d');
    $format   = $format ?: $config->get('logger')->format;
#   $filename = trim($filename ?: $config->get('logger')->filename, '\\/');
    $filename = trim($filename ?: "app-$today.log", '\\/');
    $path     = rtrim($config->get('logger')->path, '\\/') . DIRECTORY_SEPARATOR;

    $formatter = new FormatterLine($format, $config->get('logger')->date);
    $logger    = new FileLogger($path . $filename);

    $logger->setFormatter($formatter);
    $logger->setLogLevel($config->get('logger')->logLevel);

    return $logger;
});

class DummyServer
{
    public function put($job)
    {
        return true;
    }
}

$di->setShared('queue', function () use ($config) {
    if (isset($config->beanstalk->disabled) && $config->beanstalk->disabled) {
        return new DummyServer();
    }

    $queue = new Phalcon\Queue\Beanstalk(
        array(
            'host' => 'localhost',
            'port' => '11300'
        )
    );

    return $queue;
});

/**
 * Services for business logics
 */
$di->setShared('configService', function() {
    return new ConfigService();
});

$di->setShared('productService', function() {
    return new ProductService();
});

$di->setShared('pricelistService', function() {
    return new PricelistService();
});

$di->setShared('inventoryService', function() {
    return new InventoryService();
});

$di->setShared('orderService', function() {
    return new OrderService();
});

$di->setShared('purchaseService', function() {
    return new PurchaseService();
});

$di->setShared('shipmentService', function() {
    return new ShipmentService();
});

$di->setShared('priceAvailService', function() {
    return new PriceAvailService();
});

/**
 * Marketplace related services
 */
$di->setShared('amazonService', function() {
    return new AmazonService();
});

$di->setShared('ebayService', function() {
    return new EbayService();
});

$di->setShared('neweggService', function() {
    return new NeweggService();
});

$di->setShared('rakutenService', function() {
    return new RakutenService();
});
