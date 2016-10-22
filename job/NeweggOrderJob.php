<?php

class NeweggOrderJob
{
    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');
    }

    public function run($argv = [])
    {
        $client = new Marketplace\Newegg\Client('CA');
        $client->getOrders();

        $folder = $client->getOrderFolder();
        $this->importOrders($folder);
    }

    private function importOrders($path)
    {
        $path = trim($path, '/');

        $files = glob("$path/OrderList_*.*");
        foreach ($files as $file) {
            $this->importOrderFile($file);
        }
    }

    private function importOrderFile($file)
    {
        if ($this->alreadyImported($file)) {
            return; // the file already proccessed
        }

        try {
            $success = $this->db->insertAsDict('newegg_order_file', [
                'filename' => basename($file),
            ]);
        } catch (Exception $e) {
            return; // this should never happens
        }

        if (!($fh = @fopen($file, 'rb'))) {
            echo "Failed to open file: $file\n";
            return;
        }

        fgetcsv($fh); // skip the first line

        $count = 0;

        while(($fields = fgetcsv($fh))) {

            $order = [
                'OrderNumber'           => $fields[0],
                'OrderDateTime'         => $fields[1],
                'SalesChannel'          => $fields[2],
                'FulfillmentOption'     => $fields[3],
                'ShipToAddressLine1'    => $fields[4],
                'ShipToAddressLine2'    => $fields[5],
                'ShipToCity'            => $fields[6],
                'ShipToState'           => $fields[7],
                'ShipToZipCode'         => $fields[8],
                'ShipToCountry'         => $fields[9],
                'ShipToFirstName'       => $fields[10],
                'ShipToLastName'        => $fields[11],
                'ShipToCompany'         => $fields[12],
                'ShipToPhoneNumber'     => $fields[13],
                'OrderCustomerEmail'    => $fields[14],
                'OrderShippingMethod'   => $fields[15],
                'ItemSellerPartNo'      => $fields[16],
                'ItemNeweggNo'          => $fields[17],
                'ItemUnitPrice'         => $fields[18],
                'ExtendUnitPrice'       => $fields[19],
                'ItemUnitShippingCharge'=> $fields[20],
                'ExtendShippingCharge'  => $fields[21],
                'OrderShippingTotal'    => $fields[22],
                'GSTorHSTTotal'         => $fields[23],
                'PSTorQSTTotal'         => $fields[24],
                'OrderTotal'            => $fields[25],
                'QuantityOrdered'       => $fields[26],
                'QuantityShipped'       => $fields[27],
                'ShipDate'              => $fields[28],
                'ActualShippingCarrier' => $fields[29],
                'ActualShippingMethod'  => $fields[30],
                'TrackingNumber'        => $fields[31],
                'ShipFromAddress'       => $fields[32],
                'ShipFromCity'          => $fields[33],
                'ShipFromState'         => $fields[34],
                'ShipFromZipcode'       => $fields[35],
                'ShipFromName'          => $fields[36],
            ];

            echo basename($file), ' - ', $order['OrderNumber'], EOL;

            try {
                $success = $this->db->insertAsDict('newegg_order_report', [
                    'OrderNumber'         => $order['OrderNumber'],
                    'OrderDateTime'       => date('Y-m-d H:i:s', strtotime($order['OrderDateTime'])),
                    'SalesChannel'        => $order['SalesChannel'],
                    'FulfillmentOption'   => $order['FulfillmentOption'],
                    'OrderCustomerEmail'  => $order['OrderCustomerEmail'],
                    'OrderShippingMethod' => $order['OrderShippingMethod'],
                    'OrderShippingTotal'  => $order['OrderShippingTotal'],
                    'GSTorHSTTotal'       => $order['GSTorHSTTotal'],
                    'PSTorQSTTotal'       => $order['PSTorQSTTotal'],
                    'OrderTotal'          => $order['OrderTotal'],
                ]);

                $count++;

            } catch (Exception $e) {
                echo $e->getMessage(), EOL;
            }

            $this->saveOrderItem($order);
            $this->saveShippingAddress($order);
        }

        fclose($fh);

        echo EOL; // "$count orders imported\n";
    }

    private function saveOrderItem($order)
    {
        try {
            $success = $this->db->insertAsDict('newegg_order_item', [
                'OrderNumber'            => $order['OrderNumber'],
                'ItemSellerPartNo'       => $order['ItemSellerPartNo'],
                'ItemNeweggNo'           => $order['ItemNeweggNo'],
                'ItemUnitPrice'          => $order['ItemUnitPrice'],
                'ExtendUnitPrice'        => $order['ExtendUnitPrice'],
                'ItemUnitShippingCharge' => $order['ItemUnitShippingCharge'],
                'ExtendShippingCharge'   => $order['ExtendShippingCharge'],
                'QuantityOrdered'        => $order['QuantityOrdered'],
                'QuantityShipped'        => $order['QuantityShipped'],
                'ShipDate'               => $order['ShipDate'],
                'ActualShippingCarrier'  => $order['ActualShippingCarrier'],
                'ActualShippingMethod'   => $order['ActualShippingMethod'],
                'TrackingNumber'         => $order['TrackingNumber'],
                'ShipFromAddress'        => $order['ShipFromAddress'],
                'ShipFromCity'           => $order['ShipFromCity'],
                'ShipFromState'          => $order['ShipFromState'],
                'ShipFromZipcode'        => $order['ShipFromZipcode'],
                'ShipFromName'           => $order['ShipFromName'],
            ]);
        } catch (Exception $e) {
            //echo $e->getMessage(), EOL;
        }
    }

    private function saveShippingAddress($order)
    {
        try {
            $success = $this->db->insertAsDict('newegg_order_shipping_address', [
                'OrderNumber'        => $order['OrderNumber'],
                'ShipToAddressLine1' => $order['ShipToAddressLine1'],
                'ShipToAddressLine2' => $order['ShipToAddressLine2'],
                'ShipToCity'         => $order['ShipToCity'],
                'ShipToState'        => $order['ShipToState'],
                'ShipToZipCode'      => $order['ShipToZipCode'],
                'ShipToCountry'      => $order['ShipToCountry'],
                'ShipToFirstName'    => $order['ShipToFirstName'],
                'ShipToLastName'     => $order['ShipToLastName'],
                'ShipToCompany'      => $order['ShipToCompany'],
                'ShipToPhoneNumber'  => $order['ShipToPhoneNumber'],
            ]);
        } catch (Exception $e) {
            //echo $e->getMessage(), EOL;
        }
    }

    private function alreadyImported($file)
    {
        $file = basename($file);
        $sql = "SELECT filename FROM newegg_order_file WHERE filename='$file'";
        $result = $this->db->fetchOne($sql);
        return $result;
    }
}

include __DIR__ . '/../public/init.php';

$job = new NeweggOrderJob();
$job->run($argv);
