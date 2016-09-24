<?php
/*
 * Define custom routes. File gets included in the router service definition.
 */
$router = new Phalcon\Mvc\Router();

// TODO: restrict /ajax/* to AJAX only

$router->add('/ajax/order/detail', [
    'controller' => 'ajax',
    'action' => 'orderDetail'
])->via(array("POST"));

$router->add('/ajax/make/purchase', [
    'controller' => 'ajax',
    'action' => 'makePurchase'
])->via(array("POST"));

$router->add('/ajax/price/avail', [
    'controller' => 'ajax',
    'action' => 'priceAvail'
])->via(array("POST"));

$router->add('/ajax/pricelist/detail', [
    'controller' => 'ajax',
    'action' => 'priceListDetail'
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

# this never works, NotFoundPlugins works, see servers.php
# $router->notFound(["controller" => "error", "action" => "error404"]);

return $router;
