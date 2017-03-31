<?php

use Marketplace\Bestbuy\OrderReportFile;

class Bestbuy_Order_Downloader extends Order_Downloader
{
    protected $newOrders = [];

    public function run($argv = [])
    {
        $this->download();
    }

    public function download()
    {
        $filename = Filenames::get('bestbuy.order');
        $orderFile = new OrderReportFile($filename);

        $orders = $this->getBestbuyOrders();

        foreach ($orders as $order) {
            $orderFile->write($order);
        }

        $this->acceptNewOrders();
    }

    protected function getBestbuyOrders()
    {
        $client = new Marketplace\Bestbuy\Client();

        // orders since 7 days ago
        $start = date('Y-m-d\T00:00:00', strtotime('-7 days'));
        $end   = date('Y-m-d\T23:59:59');

        $orders = $client->listOrders($start, $end);

        // TODO: move this to Marketplace\Bestbuy\Client?
        foreach ($orders as $key => $order) {
            // order_state:
            // - WAITING_ACCEPTANCE
            // - WAITING_DEBIT_PAYMENT
            // - CANCELED
            // - RECEIVED
            if ($order['status'] != 'RECEIVED') {
               #unset($orders[$key]);
               #$this->log($order['orderId'].' '.$order['state']);
            }

            if ($order['status'] == 'WAITING_ACCEPTANCE') {
                $this->newOrders[] = $order;
            }
        }

        return $orders;
    }

    protected function acceptNewOrders()
    {
        $client = new Marketplace\Bestbuy\Client();

        foreach ($this->newOrders as $order) {
            $sku = $order['sku'];
            $orderId = $order['orderId'];
            $orderLineId = $order['orderItemId'];

            $msku = $this->getMasterSku($sku);

            if ($msku && $msku['overall_qty'] > 0) {
                $this->log(__METHOD__ . " Order $orderId accepted");
                $client->acceptOrder($orderId, $orderLineId);
            } else {
                $this->error(__METHOD__ . " cannot accept order $orderId, $sku is out of stock");
            }
        }
    }
}
