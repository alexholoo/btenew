<?php

include 'classes/Job.php';

class RakutenOrderJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $client = new Marketplace\Rakuten\Client('US');
        $client->getOrders();

        $folder = $client->getOrderFolder();
        $this->importOrders($folder);
    }

    private function importOrders($path)
    {
        $path = trim($path, '/');

        $files = glob("$path/23267604_*.*");
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
            $success = $this->db->insertAsDict('rakuten_order_file', [
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
        while(($fields = fgetcsv($fh, 0, "\t"))) {
            if (count($fields) < 2) continue;

            $order = [
                'SellerShopperNumber' => $fields[0],
                'Receipt_ID'          => $fields[1],
                'Receipt_Item_ID'     => $fields[2],
                'ListingID'           => $fields[3],
                'Date_Entered'        => date('Y-m-d H:i:s', strtotime($fields[4])),
                'Sku'                 => $fields[5],
                'ReferenceId'         => $fields[6],
                'Quantity'            => $fields[7],
                'Qty_Shipped'         => $fields[8],
                'Qty_Cancelled'       => $fields[9],
                'Title'               => $fields[10],
                'Price'               => $fields[11],
                'Product_Rev'         => $fields[12],
                'Shipping_Cost'       => $fields[13],
                'ProductOwed'         => $fields[14],
                'ShippingOwed'        => $fields[15],
                'Commission'          => $fields[16],
                'ShippingFee'         => $fields[17],
                'PerItemFee'          => $fields[18],
                'Tax_Cost'            => $fields[19],
                'Bill_To_Company'     => $fields[20],
                'Bill_To_Phone'       => $fields[21],
                'Bill_To_Fname'       => $fields[22],
                'Bill_To_Lname'       => $fields[23],
                'Email'               => $fields[24],
                'Ship_To_Name'        => $fields[25],
                'Ship_To_Company'     => $fields[26],
                'Ship_To_Street1'     => $fields[27],
                'Ship_To_Street2'     => $fields[28],
                'Ship_To_City'        => $fields[29],
                'Ship_To_State'       => $fields[30],
                'Ship_To_Zip'         => $fields[31],
                'ShippingMethodId'    => $fields[32],
            ];

            echo $order['Receipt_ID'], ' - ', basename($file), EOL;

            try {
                $success = $this->db->insertAsDict('rakuten_order_report', [
                    'SellerShopperNumber' => $order['SellerShopperNumber'],
                    'Receipt_ID'          => $order['Receipt_ID'],
                    'Date_Entered'        => $order['Date_Entered'],
                    'Shipping_Cost'       => $order['Shipping_Cost'],
                    'ProductOwed'         => $order['ProductOwed'],
                    'ShippingOwed'        => $order['ShippingOwed'],
                    'Commission'          => $order['Commission'],
                    'ShippingFee'         => $order['ShippingFee'],
                    'PerItemFee'          => $order['PerItemFee'],
                    'Tax_Cost'            => $order['Tax_Cost'],
                    'Bill_To_Company'     => $order['Bill_To_Company'],
                    'Bill_To_Phone'       => $order['Bill_To_Phone'],
                    'Bill_To_Fname'       => $order['Bill_To_Fname'],
                    'Bill_To_Lname'       => $order['Bill_To_Lname'],
                    'Email'               => $order['Email'],
                    'ShippingMethodId'    => $order['ShippingMethodId'],
                ]);

                $count++;

                $this->saveOrderItem($order);
                $this->saveShippingAddress($order);

            } catch (Exception $e) {
                // echo $e->getMessage(), EOL;
            }
        }

        fclose($fh);

        // echo basename($file), ": $count orders imported\n";
    }

    private function saveOrderItem($order)
    {
        try {
            $success = $this->db->insertAsDict('rakuten_order_item', [
                'Receipt_ID'          => $order['Receipt_ID'],
                'Receipt_Item_ID'     => $order['Receipt_Item_ID'],
                'ListingID'           => $order['ListingID'],
                'Sku'                 => $order['Sku'],
                'ReferenceId'         => $order['ReferenceId'],
                'Quantity'            => $order['Quantity'],
                'Qty_Shipped'         => $order['Qty_Shipped'],
                'Qty_Cancelled'       => $order['Qty_Cancelled'],
                'Title'               => $order['Title'],
                'Price'               => $order['Price'],
                'Product_Rev'         => $order['Product_Rev'],
            ]);
        } catch (Exception $e) {
            // echo $e->getMessage(), EOL;
        }
    }

    private function saveShippingAddress($order)
    {
        try {
            $success = $this->db->insertAsDict('rakuten_order_shipping_address', [
                'Receipt_ID'          => $order['Receipt_ID'],
                'Ship_To_Name'        => $order['Ship_To_Name'],
                'Ship_To_Company'     => $order['Ship_To_Company'],
                'Ship_To_Street1'     => $order['Ship_To_Street1'],
                'Ship_To_Street2'     => $order['Ship_To_Street2'],
                'Ship_To_City'        => $order['Ship_To_City'],
                'Ship_To_State'       => $order['Ship_To_State'],
                'Ship_To_Zip'         => $order['Ship_To_Zip'],
                // Country
                // Phone
            ]);
        } catch (Exception $e) {
            // echo $e->getMessage(), EOL;
        }
    }

    private function alreadyImported($file)
    {
        $file = basename($file);
        $sql = "SELECT filename FROM rakuten_order_file WHERE filename='$file'";
        $result = $this->db->fetchOne($sql);
        return $result;
    }
}

include __DIR__ . '/../public/init.php';

$job = new RakutenOrderJob();
$job->run($argv);
