<?php

class Bestbuy_Order_Merger extends OrderMerger
{
    public function run($argv = [])
    {
        try {
            $this->merge();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function merge()
    {
        $channel = 'Bestbuy';
        $filename = Filenames::get('bestbuy.order');

        $orderFile = new Marketplace\Bestbuy\OrderReportFile($filename);

        while ($order = $orderFile->read()) {
            if ($order['status'] == 'CANCELED') {
                continue;
            }
            $this->masterFile->write([
                $channel,
                $order['date'],
                $order['orderId'],
                $order['orderItemId'],
                $order['bestbuyId'], // reference
                $order['express'],
                $order['buyer'],
                $order['address'],
                $order['city'],
                $order['state'],
                $order['zipcode'],
                $order['country'],
                $order['phone'],
                '', // 'email',
                $order['sku'],
                $order['price'],
                $order['qty'],
                $order['shipping'],
            ]);
        }
    }
}
