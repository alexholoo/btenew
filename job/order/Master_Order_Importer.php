<?php

class Master_Order_Importer extends Job
{
    protected $orders;
    protected $newOrders = [];

    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $this->loadMasterOrders();
        $this->importMasterOrders();
    }

    public function getOrders()
    {
        return $this->orders;
    }

    public function getNewOrders()
    {
        return $this->newOrders;
    }

    // return orderId-indexed array
    protected function loadMasterOrders()
    {
        $this->orders = [];

        $filename = Filenames::get('master.order');
        $masterFile = new Marketplace\MasterOrderList($filename);

       #$this->log("Loading $filename");

        while ($order = $masterFile->read()) {
            $orderId = $order['order_id'];
            $this->orders[$orderId][] = $order;
        }
    }

    protected function importMasterOrders()
    {
        foreach ($this->orders as $orderId => $orders) {
            if (!$this->orderExists($orderId)) {
                $this->log("Importing order $orderId");
                $this->importOrder($orders[0]);
                $this->importOrderShippingAddress($orders[0]);
                $this->importOrderItems($orders);
            }
        }
    }

    protected function importOrder($order)
    {
        try {
            $this->db->insertAsDict('master_order', [
                'channel'   => $order['channel'],
                'date'      => $order['date'],
                'order_id'  => $order['order_id'],
                'express'   => $order['express'],
                'shipping'  => $order['shipping'],
            ]);
        } catch (\Exception $e) {
            //echo $e->getMessage(), EOL;
        }
    }

    protected function importOrderItems($orders)
    {
        foreach ($orders as $order) {
            try {
                $this->db->insertAsDict('master_order_item', [
                    'order_id'      => $order['order_id'],
                    'sku'           => $order['sku'],
                    'price'         => $order['price'],
                    'qty'           => $order['qty'],
                   #'product_name'  => $order['product_name'],
                ]);

                if (empty($order['order_item_id'])) {
                    $order['order_item_id'] = $this->db->lastInsertId();
                }

                $this->importOrderStatus($order);

                $this->newOrders[] = $order;
            } catch (\Exception $e) {
                //echo $e->getMessage(), EOL;
            }
        }
    }

    protected function importOrderShippingAddress($order)
    {
        try {
            $this->db->insertAsDict('master_order_shipping_address', [
                'date'       => $order['date'],
                'order_id'   => $order['order_id'],
                'buyer'      => $order['buyer'],
                'address'    => $order['address'],
                'city'       => $order['city'],
                'province'   => $order['province'],
                'postalcode' => $order['postalcode'],
                'country'    => $order['country'],
                'phone'      => $order['phone'],
                'email'      => $order['email'],
            ]);
        } catch (\Exception $e) {
            //echo $e->getMessage(), EOL;
        }
    }

    protected function importOrderStatus($order)
    {
        try {
            $this->db->insertAsDict('master_order_status', [
                'order_item_id' => $order['order_item_id'],
                'date'          => $order['date'],
                'channel'       => $order['channel'],
                'order_id'      => $order['order_id'],
                'stock_status'  => '',
                'supplier'      => '',
                'supplier_sku'  => '',
                'mfrpn'         => '',
                'ponum'         => '',
                'invoice'       => '',
                'ship_method'   => '',
                'trackingnum'   => '',
                'remarks'       => '',
                'flag'          => '',
                'related_sku'   => '',
                'dimension'     => '',
            ]);
        } catch (\Exception $e) {
            //echo $e->getMessage(), EOL;
        }
    }

    private function orderExists($orderId)
    {
        $sql = "SELECT order_id FROM master_order WHERE order_id='$orderId'";
        return $this->db->fetchOne($sql);
    }
}
