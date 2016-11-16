<?php

use Supplier\Supplier;
use Supplier\Model\Order;

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
        $shippingAddress = $this->getShippingAddress();

        $shoppingCartOrders = $this->getShoppingCartOrders();

        foreach ($shoppingCartOrders as $supplier => $orders) {
            $info = [];
            $info['orderId'] = $supplier.'-'.date('Ymd-Hi');
            $info['branch'] = $this->getDefaultBranch($supplier);

            echo $info['orderId'], EOL;
            print_r(array_column($orders, 'sku'));

            if ($supplier == \Supplier\Prefix::SYNNEX) {
                $orders = $this->getSynnexPrices($orders);
            }

            $order = new Order($info);
            $order->setItems($orders);
            $order->setAddress($shippingAddress);

            $client = Supplier::createClient($supplier);
            if ($client) {
#               $result = $client->purchaseOrder($order);
#               $this->removeOrdersInShoppingCart($info['orderId'], $result->orderNo, $orders);
                $this->removeOrdersInShoppingCart($info['orderId'], '', $orders);
            }
        }
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

    protected function removeOrdersInShoppingCart($orderId, $ponum, $orders)
    {
        //$this->di->get('dropshipService')->removeOrdersInShoppingCart();

        foreach ($orders as $order) {
            $id = $order['order_id'];
            $sql = "DELETE FROM shopping_cart WHERE order_id='$id'";
#           $this->db->execute($sql);

            try {
                $this->db->insertAsDict('purchase_order_log', [
                    'sku'      => $order['sku'],
                    'orderid'  => $order['order_id'],
                    'ponumber' => $ponum,
                    'flag'     => $orderId, // 'btebuy',
                ]);
            } catch (\Exception $e) {
                // echo $e->getMessage(), EOL;
            }
        }
    }

    protected function getShippingAddress()
    {
        $bte = $this->di->get('config')->bte;

        $address['buyer']      = $bte->contact;
        $address['buyer']      = $bte->name;
        $address['address']    = $bte->address;
        $address['city']       = $bte->city;
        $address['province']   = $bte->province;
        $address['postalcode'] = $bte->zipcode;
        $address['country']    = $bte->country;
        $address['phone']      = $bte->phone;
        $address['email']      = $bte->email;

        return $address;
    }

    protected function getDefaultBranch($supplier)
    {
        $defaultBranches = [
            \Supplier\Prefix::DH       => 'Toronto',
            \Supplier\Prefix::INGRAM   => \Supplier\Ingram\Warehouse::TORONTO, // 40
            \Supplier\Prefix::SYNNEX   => \Supplier\Synnex\Warehouse::MARKHAM, // 57
            \Supplier\Prefix::TECHDATA => \Supplier\Techdata\Warehouse::MISSISSAUGA, // A1
        ];

        return isset($defaultBranches[$supplier]) ? $defaultBranches[$supplier] : '';
    }

    protected function getSynnexPrices($orders)
    {
        $client = Supplier::createClient(\Supplier\Prefix::SYNNEX);

        foreach ($orders as $key => $order) {
            $result = $client->getPriceAvailability($order['sku']);
            $item = $result->getFirst();
            $orders[$key]['price'] = $item->price;
        }

        return $orders;
    }
}

include __DIR__ . '/../public/init.php';

$job = new ShoppingCartCheckoutJob();
$job->run($argv);
