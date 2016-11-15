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

$router->add('/ajax/freight/quote', [
    'controller' => 'ajax',
    'action' => 'freightQuote'
])->via(array("POST"));

$router->add('/ajax/fbaitem/delete', [
    'controller' => 'amazon',
    'action' => 'fbaItemDelete'
])->via(array("POST"));

$router->add('/ajax/shoppingcart/add', [
    'controller' => 'ajax',
    'action' => 'shoppingCartAdd'
])->via(array("POST"));

$router->add('/ajax/shoppingcart/checkout', [
    'controller' => 'ajax',
    'action' => 'shoppingCartCheckout'
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

# this never works, NotFoundPlugins works, see servers.php
# $router->notFound(["controller" => "error", "action" => "error404"]);

return $router;
