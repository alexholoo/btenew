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
use Phalcon\Flash\Direct as FlashDirect;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Logger\Formatter\Line as FormatterLine;
use Phalcon\Logger;
use Phalcon\Events\Manager as EventsManager;

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
 * $config['assetCache'] = '20160630174000';
 * "*.css?v=".$config->get('assetCache')
 */
$di->get('assets')->addCss("/assets/css/style.css?v=".filemtime(BASE_DIR.'/public/assets/css/style.css'));
$di->get('assets')->addJs("/assets/js/script.js?v=".filemtime(BASE_DIR.'/public/assets/js/script.js'));

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
    $evtMgr->attach('dispatch:beforeException', new App\Plugins\NotFoundPlugin);
#   $evtMgr->attach('dispatch:beforeDispatch',  new App\Plugins\SecurityPlugin);

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
    return new FlashSession(array(
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ));
});

$di->set('auth', function () { return new App\Library\Auth\Auth(); });
$di->set('mail', function () { return new App\Library\Mail\Mail(); });
$di->set('acl',  function () { return new App\Library\Acl\Acl(); });

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

$di->set('loggerService', function() { return new \Service\LoggerService(); });

$di->setShared('errLogger', function() use ($di) {
    return $di->get('loggerService');
});

$di->setShared('jobLogger', function() use ($di) {
    $logger = $di->get('loggerService');
    $logger->setFilename('job.log');
    return $logger;
});

$di->setShared('queue', function () use ($config) {
    if (isset($config->beanstalk->disabled) && $config->beanstalk->disabled) {
        return new class {
            public function put($job)
            {
                return true;
            }
        };
    }

    $queue = new Phalcon\Queue\Beanstalk(
        array(
            'host' => 'localhost',
            'port' => '11300'
        )
    );

    return $queue;
});

$di->setShared('redis', function() {
    $redis = new \Redis();
    $redis->connect('127.0.0.1');
    return $redis;
});

/**
 * Services for business logics
 */
$di->setShared('configService',       function() { return new \Service\ConfigService(); });
$di->setShared('productService',      function() { return new \Service\ProductService(); });
$di->setShared('pricelistService',    function() { return new \Service\PricelistService(); });
$di->setShared('inventoryService',    function() { return new \Service\InventoryService(); });
$di->setShared('orderService',        function() { return new \Service\OrderService(); });
$di->setShared('dropshipService',     function() { return new \Service\DropshipService(); });
$di->setShared('shoppingCartService', function() { return new \Service\ShoppingCartService(); });
$di->setShared('purchaseService',     function() { return new \Service\PurchaseService(); });
$di->setShared('shipmentService',     function() { return new \Service\ShipmentService(); });
$di->setShared('priceAvailService',   function() { return new \Service\PriceAvailService(); });
$di->setShared('chitchatService',     function() { return new \Service\ChitchatService(); });
$di->setShared('overstockService',    function() { return new \Service\OverstockService(); });
$di->setShared('skuService',          function() { return new \Service\SkuService(); });
$di->setShared('rmaService',          function() { return new \Service\RmaService(); });

$di->setShared('inventoryLocationService',    function() { return new \Service\InventoryLocationService(); });

/**
 * Marketplace related services
 */
$di->setShared('amazonService',     function() { return new \Service\AmazonService(); });
$di->setShared('ebayService',       function() { return new \Service\EbayService(); });
$di->setShared('neweggService',     function() { return new \Service\NeweggService(); });
$di->setShared('rakutenService',    function() { return new \Service\RakutenService(); });
$di->setShared('bestbuyService',    function() { return new \Service\BestbuyService(); });

$di->setShared('order.seq.num',     function() { return new \Toolkit\OrderSerialNumber(); });
