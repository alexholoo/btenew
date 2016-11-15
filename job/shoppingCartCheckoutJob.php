<?php

use Supplier\Supplier;

class ShoppingCartCheckoutJob
{
    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');
    }

    public function run($argv = [])
    {
        $shoppingCartOrders = $this->getShoppingCartOrders();

        foreach ($shoppingCartOrders as $supplier => $orders) {
            $client = Supplier::createClient($supplier);
            $client->batchPurchase($orders);
        }

        $this->removeOrdersInShoppingCart();
    }

    protected function getShoppingCartOrders()
    {
        $result = [];

        $orders = $this->di->get('dropshipService')->getOrdersInShoppingCart(null);

        foreach ($orders as $order) {
            $parts = explode('-', $order['sku']);
            $supplier = strtoupper($parts[0]);
            $result[$supplier][] = $order;
        }

        return $result;
    }

    protected function removeOrdersInShoppingCart()
    {
        $this->di->get('dropshipService')->removeOrdersInShoppingCart();
    }
}

include __DIR__ . '/../public/init.php';

$job = new ShoppingCartCheckoutJob();
$job->run($argv);
