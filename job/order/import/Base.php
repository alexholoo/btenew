<?php

abstract class Order_Importer extends Job
{
    abstract public function import();

    protected function importMasterOrders($masterOrders)
    {
        foreach ($masterOrders as $orderId => $orders) {
            if (!$this->orderExists($orderId)) {
                $this->log("Importing order $orderId");
                $this->insertOrder($orders[0]);
                $this->insertOrderShippingAddress($orders[0]);
                $this->insertOrderItems($orders);
            }
        }
    }

    protected function insertOrder($order)
    {
        try {
            $this->db->insertAsDict('master_order', [
                'channel'   => $order['channel'],
                'date'      => $order['date'],
                'order_id'  => $order['orderId'],
                'express'   => $order['express'],
                'shipping'  => $order['shipping'],
            ]);
        } catch (\Exception $e) {
            //echo $e->getMessage(), EOL;
        }
    }

    protected function insertOrderItems($orders)
    {
        foreach ($orders as $order) {
            try {
                $this->db->insertAsDict('master_order_item', [
                    'order_id'      => $order['orderId'],
                   #'order_item_id' => $order['orderItemId'],
                    'sku'           => $order['sku'],
                    'price'         => $order['price'],
                    'qty'           => $order['qty'],
                    'product_name'  => $order['productName'],
                ]);

                if (empty($order['orderItemId'])) {
                    $order['orderItemId'] = $this->db->lastInsertId();
                }

                $this->insertOrderStatus($order);
            } catch (\Exception $e) {
                //echo $e->getMessage(), EOL;
            }
        }
    }

    protected function insertOrderShippingAddress($order)
    {
        try {
            $this->db->insertAsDict('master_order_shipping_address', [
                'date'       => $order['date'],
                'order_id'   => $order['orderId'],
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

    protected function insertOrderStatus($order)
    {
        try {
            $this->db->insertAsDict('master_order_status', [
                'order_item_id' => $order['orderItemId'],
                'date'          => $order['date'],
                'channel'       => $order['channel'],
                'order_id'      => $order['orderId'],
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

    protected function orderExists($orderId)
    {
        $sql = "SELECT order_id FROM master_order WHERE order_id='$orderId'";
        return $this->db->fetchOne($sql);
    }
}
