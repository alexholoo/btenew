<?php

use App\Models\Orders;
use App\Models\OrderNotes;
use App\Models\MasterOrder;
use App\Models\MasterOrderItem;
use App\Models\MasterOrderShippingAddress;

class OrderImportJob
{
    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');
    }

    public function run($arg = '')
    {
        $this->importOrders();
        $this->importDropship();
    }

    public function importOrders()
    {
        $file = 'E:/BTE/orders/all_mgn_orders.csv';
        if (!($fh = @fopen($file, 'rb'))) {
            echo "Failed to open file: $file\n";
            return;
        }

        echo "Importing $file\n";

        fgetcsv($fh); // skip the first line

        $count = 0;
        while(($fields = fgetcsv($fh))) {

            $channel    = $fields[0];
            $date       = $fields[1];
            $orderId   = $fields[2];
            $express    = $fields[4];
            $buyer      = utf8_encode($fields[5]);
            $address    = utf8_encode($fields[6]);
            $city       = utf8_encode($fields[7]);
            $province   = utf8_encode($fields[8]);
            $postalcode = $fields[9];
            $country    = $fields[10];
            $phone      = $fields[11];
            $email      = $fields[12];
            $sku        = $fields[13];
            $price      = $fields[14];
            $qty        = $fields[15];
            $shipping   = $fields[16];

            $order = MasterOrder::findFirst("orderId='$orderId'");

            if (!$order) {
                $order = new MasterOrder();
                $order->channel  = $channel;
                $order->date     = $date;
                $order->orderId  = $orderId;
                $order->express  = $express;
                $order->shipping = $shipping;

                if ($order->save() === false) {
                    $messages = $order->getMessages();
                    foreach ($messages as $message) {
                        echo $orderId, ': ', $message, EOL;
                    }
                } else {
                    $count++;
                }

                $shippingAddress = new MasterOrderShippingAddress();
                $shippingAddress->date       = $date;
                $shippingAddress->orderId    = $orderId;
                $shippingAddress->buyer      = $buyer;
                $shippingAddress->address    = $address;
                $shippingAddress->city       = $city;
                $shippingAddress->province   = $province;
                $shippingAddress->postalcode = $postalcode;
                $shippingAddress->country    = $country;
                $shippingAddress->phone      = $phone;
                $shippingAddress->email      = $email;

                if ($shippingAddress->save() === false) {
                    $messages = $shippingAddress->getMessages();
                    foreach ($messages as $message) {
                        echo $message, EOL;
                    }
                }
            }

            $item = MasterOrderItem::findFirst("orderId='$orderId' AND sku='$sku'");

            if (!$item) {
                $item = new MasterOrderItem();
                $item->orderId = $orderId;
                $item->sku     = $sku;
                $item->price   = $price;
                $item->qty     = $qty;

                if ($item->save() === false) {
                    $messages = $item->getMessages();
                    foreach ($messages as $message) {
                        echo $message, EOL;
                    }
                }
            }
        }

        fclose($fh);

        echo "$count orders imported\n";
    }

    public function importDropship()
    {
        $file = 'E:/BTE/orders/ca_order_notes.csv';
        if (($fh = @fopen($file, 'rb')) === false) {
            echo "Failed to open file: $file\n";
            return;
        }

        echo "Importing $file\n";

        fgetcsv($fh); // skip the first line

        $count = 0;
        while(($fields = fgetcsv($fh))) {
            $date        = $fields[0];
            $orderId     = $fields[1];
            $stockStatus = $fields[2];
            $express     = $fields[3];
            $qty         = $fields[4];
            $supplier    = $fields[5];
            $supplierSku = $fields[6];
            $mpn         = $fields[7];
            $supplierNo  = $fields[8];
            $notes       = $fields[9];
            $relatedSkus = $fields[10];
            $dimension   = $fields[11];

            $order = OrderNotes::findFirst("orderId='$orderId'");

            if (!$order) {
                $order = new OrderNotes();
            }

            $order->date        = $date;
            $order->orderId     = $orderId;
            $order->stockStatus = $stockStatus;
            $order->express     = $express;
            $order->qty         = $qty;
            $order->supplier    = $supplier;
            $order->supplierSku = $supplierSku;
            $order->mpn         = $mpn;
            $order->supplierNo  = $supplierNo;
            $order->notes       = $notes;
            $order->relatedSkus = $relatedSkus;
            $order->dimension   = $dimension;

            try {
                if ($order->save() === false) {
                    $messages = $order->getMessages();
                    foreach ($messages as $message) {
                        echo $orderId, ': ', $message, EOL;
                    }
                } else {
                    $count++;
                }
            } catch (\Exception $e) {
                echo $e->getMessage(), EOL;
            }
        }

        fclose($fh);

        echo "$count dropship orders imported\n";
    }
}

include __DIR__ . '/../public/init.php';

$job = new OrderImportJob();
$job->run();
