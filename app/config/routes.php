<?php
/*
 * Define custom routes. File gets included in the router service definition.
 */
$router = new Phalcon\Mvc\Router();

$router->add('/ajax/fbaitem/delete', [
    'controller' => 'amazon',
    'action' => 'fbaItemDelete'
])->via(array("POST"));

// aliases
$router->add('/purchase/assist', [
    'controller' => 'purchase',
    'action' => 'index'
]);

$router->add('/dropship', [
    'controller' => 'purchase',
    'action' => 'index'
]);

$router->add('/fbaitems', [
    'controller' => 'amazon',
    'action' => 'fbaitems'
]);

$router->add('/api/:controller/:action/:params', [
    'namespace'  => 'Api\Controllers',
    "controller" => 1,
    "action"     => 2,
    "params"     => 3,
]);

$router->add('/ajax/:controller/:action/:params', [
    'namespace'  => 'Ajax\Controllers',
    "controller" => 1,
    "action"     => 2,
    "params"     => 3,
]);

# this never works, NotFoundPlugins works, see servers.php
# $router->notFound(["controller" => "error", "action" => "error404"]);

return $router;
