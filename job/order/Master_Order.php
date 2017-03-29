<?php

class Master_Order extends Job
{
    public function run($argv = [])
    {
        $filename = Filenames::get('master.order');
        $masterFile = new Marketplace\MasterOrderList($filename);

        $this->importAmazonOrders($masterFile, 'CA');
        $this->importAmazonOrders($masterFile, 'US');

        $this->importBestbuyOrders($masterFile);

        $this->importEbayOrders($masterFile, 'BTE');
        $this->importEbayOrders($masterFile, 'ODO');

        $this->importNeweggOrders($masterFile, 'CA');
        $this->importNeweggOrders($masterFile, 'US');

        $this->importRakutenOrders($masterFile);
    }

    public function importAmazonOrders($masterFile, $site)
    {
        if ($site == 'CA') {
            $channel = 'AmazonACA';
            $filename = Filenames::get('amazon.ca.order');
        }

        if ($site == 'US') {
            $channel = 'AmazonUS';
            $filename = Filenames::get('amazon.us.order');
        }

        $orderFile = new Marketplace\Amazon\StdOrderReportFile($filename);
        while ($order = $orderFile->read()) {
            $masterFile->write([
                $order[''],     // 'channel',
                $order[''],     // 'date',
                $order[''],     // 'channel_order_id',
                $order[''],     // 'order_item_id',
                $order[''],     // 'express',
                $order[''],     // 'buyer',
                $order[''],     // 'address',
                $order[''],     // 'city',
                $order[''],     // 'province',
                $order[''],     // 'postalcode',
                $order[''],     // 'country',
                $order[''],     // 'phone',
                $order[''],     // 'email',
                $order[''],     // 'sku',
                $order[''],     // 'price',
                $order[''],     // 'qty',
                $order[''],     // 'shipping',
            ]);
        }
    }

    public function importBestbuyOrders($masterFile)
    {
        $channel = 'Bestbuy';
        $filename = Filenames::get('bestbuy.order');

        $orderFile = new Marketplace\Bestbuy\OrderReportFile($filename);
        while ($order = $orderFile->read()) {
            $masterFile->write([
                $channel,       // 'channel',
                $order[''],     // 'date',
                $order[''],     // 'channel_order_id',
                $order[''],     // 'order_item_id',
                $order[''],     // 'express',
                $order[''],     // 'buyer',
                $order[''],     // 'address',
                $order[''],     // 'city',
                $order[''],     // 'province',
                $order[''],     // 'postalcode',
                $order[''],     // 'country',
                $order[''],     // 'phone',
                $order[''],     // 'email',
                $order[''],     // 'sku',
                $order[''],     // 'price',
                $order[''],     // 'qty',
                $order[''],     // 'shipping',
            ]);
        }
    }

    public function importEbayOrders($masterFile, $site)
    {
        if ($site == 'BTE') {
            $channel = 'eBay-BTE';
            $filename = Filenames::get('ebay.bte.order');
        }

        if ($site == 'ODO') {
            $channel = 'eBay-ODO';
            $filename = Filenames::get('ebay.odo.order');
        }

        $orderFile = new Marketplace\eBay\OrderReportFile($filename);
        while ($order = $orderFile->read()) {
            $masterFile->write([
                $channel,       // 'channel',
                $order[''],     // 'date',
                $order[''],     // 'channel_order_id',
                $order[''],     // 'order_item_id',
                $order[''],     // 'express',
                $order[''],     // 'buyer',
                $order[''],     // 'address',
                $order[''],     // 'city',
                $order[''],     // 'province',
                $order[''],     // 'postalcode',
                $order[''],     // 'country',
                $order[''],     // 'phone',
                $order[''],     // 'email',
                $order[''],     // 'sku',
                $order[''],     // 'price',
                $order[''],     // 'qty',
                $order[''],     // 'shipping',
            ]);
        }
    }

    public function importNeweggOrders($masterFile, $site)
    {
        if ($site == 'CA') {
            $channel = 'NeweggCA';
            $filename = Filenames::get('newegg.ca.master.order');
        }

        if ($site == 'US') {
            $channel = 'NeweggUSA';
            $filename = Filenames::get('newegg.us.master.order');
        }

        $orderFile = new Marketplace\Newegg\StdOrderListFile($filename);
        while ($order = $orderFile->read()) {
            $masterFile->write([
                $channel,       // 'channel',
                $order[''],     // 'date',
                $order[''],     // 'channel_order_id',
                $order[''],     // 'order_item_id',
                $order[''],     // 'express',
                $order[''],     // 'buyer',
                $order[''],     // 'address',
                $order[''],     // 'city',
                $order[''],     // 'province',
                $order[''],     // 'postalcode',
                $order[''],     // 'country',
                $order[''],     // 'phone',
                $order[''],     // 'email',
                $order[''],     // 'sku',
                $order[''],     // 'price',
                $order[''],     // 'qty',
                $order[''],     // 'shipping',
            ]);
        }
    }

    public function importRakutenOrders($masterFile)
    {
        $channel = 'Rakuten-BUY';
        $filename = Filenames::get('rakuten.us.master.order');

        $orderFile = new Marketplace\Newegg\StdOrderListFile($filename);
        while ($order = $orderFile->read()) {
            $masterFile->write([
                $channel,       // 'channel',
                $order[''],     // 'date',
                $order[''],     // 'channel_order_id',
                $order[''],     // 'order_item_id',
                $order[''],     // 'express',
                $order[''],     // 'buyer',
                $order[''],     // 'address',
                $order[''],     // 'city',
                $order[''],     // 'province',
                $order[''],     // 'postalcode',
                $order[''],     // 'country',
                $order[''],     // 'phone',
                $order[''],     // 'email',
                $order[''],     // 'sku',
                $order[''],     // 'price',
                $order[''],     // 'qty',
                $order[''],     // 'shipping',
            ]);
        }
    }
}
