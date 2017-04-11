<?php

include __DIR__ . '/../public/init.php';

class MasterOrderJob extends Job
{
    protected $orders;
    protected $newOrders = [];

    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $this->getOrders();
        $this->importMaster();

        $this->newOrderTrigger();
    }

    protected function getOrders()
    {
        $this->orders = [];

        $filename = 'w:/out/shipping/all_mgn_orders.csv';
        if (IS_PROD) {
            $filename = 'E:/BTE/import/all_mgn_orders.csv';
        }

        if (!($handle = @fopen($filename, 'rb'))) {
            $this->log("Failed to open file: $filename");
            return;
        }

        $this->log("Loading $filename");

        $title = fgetcsv($handle);

        $columns = [
             'channel',
             'date',
             'order_id',
             'mgn_order_id',
             'express',
             'buyer',
             'address',
             'city',
             'province',
             'postalcode',
             'country',
             'phone',
             'email',
             'sku',
             'price',
             'qty',
             'shipping',
             'product_name',
        ];

        while (($fields = fgetcsv($handle))) {
            $orderId = $fields[2];
            if (count($columns) != count($fields)) {
                $this->error(__METHOD__.' Error: '. $orderId);
                continue;
            }
            $order = array_combine($columns, $fields);
            $this->orders[$orderId][] = $order;
        }

        fclose($handle);
    }

    protected function importMaster()
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
                    'product_name'  => $order['product_name'],
                ]);

                $order['order_item_id'] = $this->db->lastInsertId();
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

    protected function newOrderTrigger()
    {
        $this->log(count($this->newOrders). " new orders");

        if (count($this->newOrders) > 0) {

            $triggers = $this->getTriggers();

            foreach ($triggers as $trigger) {
                echo EOL;
                $trigger->run();
            }
        }
    }

    protected function getTriggers()
    {
        $triggers = [];

        // base class for all order triggers
        include_once('order/triggers/Base.php');

        foreach (glob("order/triggers/*.php") as $filename) {
            include_once $filename;

            $path = pathinfo($filename);
            $class = $path['filename'];

            if (class_exists($class)) {
                $trigger = new $class;
                $trigger->setOrders($this->newOrders);

                $priority = $trigger->getPriority();

                if ($priority > 0) {
                    $triggers[] = [
                        'priority' => $priority,
                        'trigger'  => $trigger,
                    ];
                }
            }
        }

        // Trigger with smaller priority runs first
        usort($triggers, function($a, $b) {
            return $a['priority'] > $b['priority'];
        });

        return array_column($triggers, 'trigger', 'priority');
    }

    private function orderExists($orderId)
    {
        $sql = "SELECT order_id FROM master_order WHERE order_id='$orderId'";
        return $this->db->fetchOne($sql);
    }
}

$job = new MasterOrderJob();
$job->run($argv);
