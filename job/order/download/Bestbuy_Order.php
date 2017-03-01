<?php

class Bestbuy_Order extends OrderDownloader
{
    public function download()
    {
        $filename = Filenames::get('bestbuy.order');
        $orderFile = new Marketplace\Bestbuy\OrderReportFile($filename);

        $orders = $this->getBestbuyOrders();

        foreach ($orders as $order) {
            $orderFile->write($order);
        }
    }

    protected function getBestbuyOrders()
    {
        $client = new Marketplace\Bestbuy\Client();

        // only today's orders
        $start = date('Y-m-d\T00:00:00');
        $end   = date('Y-m-d\T23:59:59');

        $orders = $client->listOrders($start, $end);

        // TODO: move this to Marketplace\Bestbuy\Client?
        foreach ($orders as $key => $order) {
            // order_state:
            // - WAITING_ACCEPTANCE
            // - WAITING_DEBIT_PAYMENT
            // - CANCELED
            // - RECEIVED
            if ($order['state'] != 'RECEIVED') {
                unset($orders[$key]);
               #$this->log($order['orderId'].' '.$order['state']);
            }
        }

        return $orders;
    }
}
