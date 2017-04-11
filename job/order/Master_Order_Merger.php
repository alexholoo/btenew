<?php

class Master_Order_Merger extends Job
{
    public function run($argv = [])
    {
        $filename = Filenames::get('master.order');
        $masterFile = new Marketplace\MasterOrderList($filename);

        try {
            $this->importAmazonOrders($masterFile, 'CA');
            $this->importAmazonOrders($masterFile, 'US');

            $this->importBestbuyOrders($masterFile);

            $this->importEbayOrders($masterFile, 'GFS');
            $this->importEbayOrders($masterFile, 'ODO');

            $this->importNeweggOrders($masterFile, 'CA');
            $this->importNeweggOrders($masterFile, 'US');

            $this->importRakutenOrders($masterFile);
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function importAmazonOrders($masterFile, $site)
    {
        if ($site == 'CA') {
            $channel = 'Amazon-ACA';
            $filename = Filenames::get('amazon.ca.order');
        }

        if ($site == 'US') {
            $channel = 'Amazon-US';
            $filename = Filenames::get('amazon.us.order');
        }

        $orderFile = new Marketplace\Amazon\OrderReportFile($filename, $site);

        while ($order = $orderFile->read()) {
            $express = strpos($order['ShipServiceLevel'], 'Exp') !== false ? 1 : 0;
            $address = trim($order['Address1'].' '.$order['Address2'].' '.$order['Address3']);

            $masterFile->write([
                $channel,
                substr($order['Date'], 0, 10),
                $order['OrderId'],
                $order['OrderItemId'],
                $order['OrderId'], // reference
                $express,
                $order['Name'],
                $address,
                $order['City'],
                $order['StateOrRegion'],
                $order['PostalCode'],
                $order['CountryCode'],
                $order['Phone'],
                $order['BuyerEmail'],
                $order['SellerSKU'],
                $order['ItemPrice'],
                $order['Quantity'],
                $order['ShippingPrice'],
            ]);
        }
    }

    public function importBestbuyOrders($masterFile)
    {
        $channel = 'Bestbuy';
        $filename = Filenames::get('bestbuy.order');

        $orderFile = new Marketplace\Bestbuy\OrderReportFile($filename);

        while ($order = $orderFile->read()) {
            if ($order['status'] == 'CANCELED') {
                continue;
            }
            $masterFile->write([
                $channel,
                $order['date'],
                $order['orderId'],
                $order['orderItemId'],
                $order['bestbuyId'],
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

    public function importEbayOrders($masterFile, $site)
    {
        if ($site == 'GFS') {
            $channel = 'eBay-GFS';
            $filename = Filenames::get('ebay.gfs.order');
        }

        if ($site == 'ODO') {
            $channel = 'eBay-ODO';
            $filename = Filenames::get('ebay.odo.order');
        }

        $orderFile = new Marketplace\eBay\OrderReportFile($filename);

        while ($order = $orderFile->read()) {
            $express = ($order['ShippingService'] == 'ShippingMethodExpress') ? 1 : 0;
            $masterFile->write([
                $channel,
                $order['DatePaid'],
                $order['OrderID'],
                $order['ItemID'],
                $order['RecordNumber'], // reference
                $express,
                $order['Name'],
                $order['Address'].' '.$order['Address2'],
                $order['City'],
                $order['Province'],
                $order['PostalCode'],
                $order['Country'],
                $order['Phone'],
                $order['Email'],
                $order['SKU'],
                $order['TransactionPrice'],
                $order['QuantityPurchased'],
                $order['ShippingServiceCost'],
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

        $orderFile = new Marketplace\Newegg\StdOrderListFile($filename, $site);

        while ($order = $orderFile->read()) {
            $address = $order['Ship To Address Line 1'].' '.$order['Ship To Address Line 2'];
            $express = preg_match('/Standard|Economy/', $order['Order Shipping Method']) ? 0 : 1;
            $buyer = $order['Ship To First Name'].' '.$order['Ship To LastName'];

            $masterFile->write([
                $channel,
                date('Y-m-d', strtotime($order['Order Date & Time'])),
                $order['Order Number'],
                $order['Item Newegg #'],
                $order['Order Number'], // reference
                $express,
                $buyer,
                $address,
                $order['Ship To City'],
                $order['Ship To State'],
                $order['Ship To ZipCode'],
                $order['Ship To Country'],
                $order['Ship To Phone Number'],
                $order['Order Customer Email'],
                $order['Item Seller Part #'],
                $order['Item Unit Price'],
                $order['Quantity Ordered'],
                $order['Item Unit Shipping Charge'],
            ]);
        }
    }

    public function importRakutenOrders($masterFile)
    {
        $channel = 'Rakuten-BUY';
        $filename = Filenames::get('rakuten.us.master.order');

        $orderFile = new Marketplace\Rakuten\StdOrderListFile($filename, 'US');

        while ($order = $orderFile->read()) {
            $address = $order['Ship_To_Street1'].' '.$order['Ship_To_Street2'];
            $express = $order['ShippingMethodId'];
            $masterFile->write([
                $channel,
                date('Y-m-d', strtotime($order['Date_Entered'])),
                $order['Receipt_ID'],
                $order['Receipt_Item_ID'],
                $order['Receipt_ID'], // reference
                $express,
                $order['Ship_To_Name'],
                $address,
                $order['Ship_To_City'],
                $order['Ship_To_State'],
                $order['Ship_To_Zip'],
                'US',     // 'country',
                $order['Bill_To_Phone'],
                $order['Email'],
                $order['ReferenceId'],
                $order['Price'],
                $order['Quantity'],
                $order['ShippingFee'],
            ]);
        }
    }
}
