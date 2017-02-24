<?php

class Newegg_Order extends OrderDownloader
{
    public function download()
    {
        return;

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
            $this->log("Failed to open file: $file");
            return;
        }

        fgetcsv($fh); // skip the first line

        $this->log("Importing Newegg Order File: $file");

        $count = 0;

        $columns = $this->getColumns();

        while(($fields = fgetcsv($fh))) {

            if (count($columns) != count($fields)) {
                $this->error(__METHOD__.' Error: '.$fields[0].' in file '.$file);
                continue;
            }

            $order = array_combine($columns, $fields);

            $this->log(basename($file).' - '.$order['OrderNumber']);

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
                //echo $e->getMessage(), EOL;
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

    private function getColumns()
    {
        return [
            'OrderNumber',
            'OrderDateTime',
            'SalesChannel',
            'FulfillmentOption',
            'ShipToAddressLine1',
            'ShipToAddressLine2',
            'ShipToCity',
            'ShipToState',
            'ShipToZipCode',
            'ShipToCountry',
            'ShipToFirstName',
            'ShipToLastName',
            'ShipToCompany',
            'ShipToPhoneNumber',
            'OrderCustomerEmail',
            'OrderShippingMethod',
            'ItemSellerPartNo',
            'ItemNeweggNo',
            'ItemUnitPrice',
            'ExtendUnitPrice',
            'ItemUnitShippingCharge',
            'ExtendShippingCharge',
            'OrderShippingTotal',
            'OrderDiscountAmount',
            'GSTorHSTTotal',
            'PSTorQSTTotal',
            'OrderTotal',
            'QuantityOrdered',
            'QuantityShipped',
            'ShipDate',
            'ActualShippingCarrier',
            'ActualShippingMethod',
            'TrackingNumber',
            'ShipFromAddress',
            'ShipFromCity',
            'ShipFromState',
            'ShipFromZipcode',
            'ShipFromName',
        ];
    }
}
