<?php

class Newegg_Order_Merger extends OrderMerger
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
        $this->mergeNeweggOrders('CA');
        $this->mergeNeweggOrders('US');
    }

    protected function mergeNeweggOrders($site)
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

        $neweggService = $this->di->get('neweggService');

        while ($order = $orderFile->read()) {
            $address = $order['Ship To Address Line 1'].' '.$order['Ship To Address Line 2'];
            $buyer = $order['Ship To First Name'].' '.$order['Ship To LastName'];

            $shipMethod = $neweggService->getShipMethodCode($order['Order Shipping Method']);

            // UPS accepts only format '12345-6789'
            $zipcode = trim($order['Ship To ZipCode']);
            if (preg_match('/^\d{9,}$/', $zipcode)) {
                $zipcode = substr($zipcode, 0, 5).'-'.substr($zipcode, 5);
            }

            $this->masterFile->write([
                $channel,
                date('Y-m-d', strtotime($order['Order Date & Time'])),
                $order['Order Number'],
                $order['Item Newegg #'],
                $order['Order Number'], // reference
                $shipMethod,
                $buyer,
                $address,
                $order['Ship To City'],
                $order['Ship To State'],
                $zipcode,
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
}
