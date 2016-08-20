<?php
/*
 * Define custom routes. File gets included in the router service definition.
 */
$router = new Phalcon\Mvc\Router();

$router->add('/ajax/order/detail', [
    'controller' => 'ajax',
    'action' => 'orderDetail'
]);

$router->add('/ajax/make/purchase', [
    'controller' => 'ajax',
    'action' => 'makePurchase'
]);

$router->add('/ajax/price/avail', [
    'controller' => 'ajax',
    'action' => 'priceAvail'
]);

return $router;
