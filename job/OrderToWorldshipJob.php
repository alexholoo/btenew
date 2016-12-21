<?php

include 'classes/Job.php';

class OrderToWorldshipJob extends Job
{
    public function run($args = [])
    {
        $this->log('>> '. __CLASS__);

        $this->importOrders();
    }

    public function importOrders()
    {
        $orders = $this->getOrders();

        $out = @fopen('w:/out/shipping/UPS/worldship.csv', 'w');
        $title = $this->getUpsTitle();
        fputcsv($out, $title);

        foreach ($orders as $order) {
           #$channel    = $order[0];
           #$date       = $order[1];
            $orderId    = $order[2];
            $express    = $order[4];
            $buyer      = $order[5];
            $address    = trim($order[6]);
            $city       = $order[7];
            $province   = $order[8];
            $postalcode = $order[9];
            $country    = $order[10];
            $phone      = $order[11];
           #$email      = $order[12];
            $sku        = $order[13];
           #$price      = $order[14];
           #$qty        = $order[15];
           #$shipping   = $order[16];
           #$product    = $order[17];

            $arr = explode("\n", wordwrap($address, 35, "\n"));
            $addr1 = $arr[0];
            $addr2 = isset($arr[1]) ? $arr[1] : '';

            $data = array_combine($title, array_fill(0, count($title), ''));

            $data['ShipmentInformation_ServiceType'] = 'GND';
            $data['ShipmentInformation_BillingOption'] = 'SHP';

            if ($express) {
                $data['ShipmentInformation_ServiceType'] = 'EX';
            }

            $data['OrderId'] = $orderId;
            $data['ShipTo_CompanyOrName'] = $buyer;
            $data['ShipTo_StreetAddress'] = $addr1;
            $data['ShipTo_RoomFloorAddress2'] = $addr2;
            $data['ShipTo_City'] = $city;
            $data['ShipTo_State'] = $province;
            $data['ShipTo_Country'] = $country;
            $data['ShipTo_ZipCode'] = $postalcode;
            $data['ShipTo_Telephone'] = $phone;

            $data['Package_PackageType'] = 'CP';
            $data['Package_Weight'] = $this->getWeight($sku);

            fputcsv($out, $data);
        }

        fclose($out);

        echo count($orders), " orders imported to worldship.csv\n";
    }

    public function getOrders()
    {
        $file = 'w:/out/shipping/all_mgn_orders.csv';
        if (gethostname() == 'BTELENOVO') {
            $file = 'E:/BTE/import/all_mgn_orders.csv';
        }

        if (!($in = @fopen($file, 'rb'))) {
            echo "Failed to open file: $file\n";
            return;
        }

        $title = fgetcsv($in);

        $start = date('Y-m-d', strtotime('-2 days'));

        $orders = [];

        while (($fields = fgetcsv($in))) {

            $data = array_combine($title, $fields);

            $channel = $data['channel'];
            $date    = $data['date'];
            $country = $data['country'];

            if ($channel == 'Amazon-US' && $country == 'US' && $date > $start) {
                $orders[] = $fields;
            }
        }

        fclose($in);

        return $orders;
    }

    public function getWeight($sku)
    {
        $sql = "SELECT weight FROM master_sku_list WHERE sku='$sku'";

        $result = $this->db->fetchOne($sql);
        if ($result) {
            return $result['weight'];
        }

        return '';
    }

    public function getUpsTitle()
    {
        return [
            'OrderId',
            'ShipmentInformation_ServiceType', //*
            'ShipmentInformation_BillingOption', //*
            'ShipmentInformation_QvnOption',
            'ShipmentInformation_QvnShipNotification1Option',
            'ShipmentInformation_NotificationRecipient1Type',
            'ShipmentInformation_NotificationRecipient1FaxorEmail',
            'ShipTo_CompanyOrName', //*
            'ShipTo_StreetAddress', //*
            'ShipTo_RoomFloorAddress2',
            'ShipTo_City', //*
            'ShipTo_State', //*
            'ShipTo_Country', //*
            'ShipTo_ZipCode', //*
            'ShipTo_Telephone',
            'ShipTo_ResidentialIndicator',
            'Package_PackageType', //*
            'Package_Weight', //*
            'Package_Reference1',
            'Package_Reference2',
            'Package_Reference3',
            'Package_Reference4',
            'Package_Reference5',
            'Package_DeclaredValueOption',
            'Package_DeclaredValueAmount',
            'ShipTo_LocationID'
        ];
    }
}

include __DIR__ . '/../public/init.php';

$job = new OrderToWorldshipJob();
$job->run($argv);
