<?php

include 'classes/Job.php';

use Toolkit\Utils;

class OrderToWorldshipJob extends Job
{
    public function run($args = [])
    {
        $this->log('>> '. __CLASS__);

       #$this->importOrdersCSV();
        $this->importOrdersXML();
    }

    protected function importOrdersCSV()
    {
        $orders = $this->getOrders();

        $out = fopen('w:/out/shipping/UPS/worldship.csv', 'w');
        $title = $this->getUpsTitle();
        #fputcsv($out, $title); // no title

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
            $phone      = Utils::formatPhoneNumber($order[11]);
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

            $data['Contact Name'] = $buyer;
            $data['Company or Name'] = $buyer;
            $data['Address 1'] = $addr1;
            $data['Address 2'] = $addr2;
            $data['City'] = $city;
            $data['State/Province/Other'] = $province;
            $data['Postal Code'] = $postalcode;
            $data['Country'] = $country;
            $data['Telephone'] = $phone;
            $data['Packaging Type'] = '4'; // TODO: fix it
            $data['Weight'] = $this->getWeight($sku);
           #$data['Description of Goods'] = $product;
            $data['Service'] = '03'; // 'GND';
            $data['Reference 1'] = $orderId;

            if ($express) {
                $data['Service'] = '07'; // 'EX';
            }

            fputcsv($out, $data);
        }

        fclose($out);

        $this->log(count($orders). " orders imported to worldship.csv");
    }

    protected function importOrdersXML()
    {
    }

    /**
     * Creating a shipment will print label immediately.
     * This is not useful for us currently, it maybe useful in the future.
     */
    protected function createShipments()
    {
        $orders = $this->getOrders();

        $lines = [];
        $lines[] = '<?xml version="1.0" encoding="WINDOWS-1252"?>';
        $lines[] = '<OpenShipments xmlns="x-schema:OpenShipments.xdr">';

        foreach ($orders as $order) {
            $lines[] = $this->createShipment($order);
        }

        $lines[] = '</OpenShipments>';

        file_put_contents('w:/out/shipping/UPS/worldship.xml', implode("\n", $lines));

        $this->log(count($orders). " orders imported to worldship.xml");
    }

    protected function createShipment($order)
    {
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
        $phone      = Utils::formatPhoneNumber($order[11]);
       #$email      = $order[12];
        $sku        = $order[13];
       #$price      = $order[14];
       #$qty        = $order[15];
       #$shipping   = $order[16];
        $product    = $order[17];

        $weight = $this->getWeight($sku);

        $arr = explode("\n", wordwrap($address, 35, "\n"));
        $addr1 = $arr[0];
        $addr2 = isset($arr[1]) ? $arr[1] : '';

        $lines = [];
        $lines[] = '<OpenShipment ProcessStatus="" ShipmentOption="">';
        $lines[] = "    <ShipTo>";
        $lines[] = "        <CompanyOrName>$buyer</CompanyOrName>";
        $lines[] = "        <Attention>$buyer</Attention>";
        $lines[] = "        <Address1>$address</Address1>";
        $lines[] = "        <CountryTerritory>$country</CountryTerritory>";
        $lines[] = "        <PostalCode>$postalcode</PostalCode>";
        $lines[] = "        <CityOrTown>$city</CityOrTown>";
        $lines[] = "        <StateProvinceCounty>$province</StateProvinceCounty>";
        $lines[] = "        <Telephone>$phone</Telephone>";
        $lines[] = "    </ShipTo>";
        $lines[] = "    <ShipFrom>";
        $lines[] = "        <CompanyOrName>BTE Computer Inc.</CompanyOrName>";
        $lines[] = "        <Attention>Roy Zhang</Attention>";
        $lines[] = "        <Address1>Unit 5, 270 Esna Park Dr</Address1>";
        $lines[] = "        <CountryTerritory>CA</CountryTerritory>";
        $lines[] = "        <PostalCode>L3R 1H3</PostalCode>";
        $lines[] = "        <CityOrTown>Markham</CityOrTown>";
        $lines[] = "        <StateProvinceCounty>ON</StateProvinceCounty>";
        $lines[] = "        <Telephone>905-480-0618</Telephone>";
        $lines[] = "        <UpsAccountNumber>37Y059</UpsAccountNumber>";
        $lines[] = "    </ShipFrom>";
        $lines[] = "    <ShipmentInformation>";
        $lines[] = "        <ServiceType>ST</ServiceType>";
        $lines[] = "        <DescriptionOfGoods>$product</DescriptionOfGoods>";
        $lines[] = "        <GoodsNotInFreeCirculation>0</GoodsNotInFreeCirculation>";
        $lines[] = "        <BillTransportationTo>Shipper</BillTransportationTo>";
        $lines[] = "    </ShipmentInformation>";
        $lines[] = "    <Package>";
        $lines[] = "        <PackageType>CP</PackageType>";
        $lines[] = "        <Weight>$weight</Weight>";
        $lines[] = "        <Length></Length>";
        $lines[] = "        <Width></Width>";
        $lines[] = "        <Height></Height>";
        $lines[] = "        <Reference1>$orderId</Reference1>";
        $lines[] = "    </Package>";
        $lines[] = "</OpenShipment>";

        return implode("\n", $lines);
    }

    protected function getOrders()
    {
        $file = 'w:/out/shipping/all_mgn_orders.csv';
        if (IS_PROD) {
            $file = 'E:/BTE/import/all_mgn_orders.csv';
        }

        if (!($in = @fopen($file, 'rb'))) {
            $this->log("Failed to open file: $file");
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

    protected function getWeight($sku)
    {
        $sql = "SELECT weight FROM master_sku_list WHERE sku='$sku'";

        $result = $this->db->fetchOne($sql);
        if ($result) {
            return $result['weight'];
        }

        return '';
    }

    protected function getUpsTitle()
    {
        return [
            'Contact Name', //*
            'Company or Name',  //*
            'Country',  //*
            'Address 1',    //*
            'Address 2',
            'Address 3',
            'City', //*
            'State/Province/Other', //*
            'Postal Code',  //*
            'Telephone',    //*
            'Ext',
            'Residential Indicator',
            'E-mail address',
            'Packaging Type',   //*
            'Customs Value',
            'Weight',   //*
            'Length',
            'Width',
            'Height',
            'Unit of Measure',
            'Description of Goods', //*
            'Documents of No Commercial value',
            'GNIFC (Goods not in Free Circulation)',
            'Package Declared Value',
            'Service',  //*
            'Delivery Confirmation',
            'Shipper Release/Deliver without Signature',
            'Return of Document',
            'Deliver on Saturday',
            'UPS Carbon Neutral',
            'Large Package',
            'Additional Handling',
            'Reference 1',
            'Reference 2',
            'Reference 3',
            'E-mail Notification 1 - Address',
            'E-mail Notification 1 - Ship',
            'E-mail Notification 1 - Exception',
            'E-mail Notification 1 - Delivery',
            'E-mail Notification 2 - Address',
            'E-mail Notification 2 - Ship',
            'E-mail Notification 2 - Exception',
            'E-mail Notification 2 - Delivery',
            'E-mail Notification 3 - Address',
            'E-mail Notification 3 - Ship',
            'E-mail Notification 3 - Exception',
            'E-mail Notification 3 - Delivery',
            'E-mail Notification 4 - Address',
            'E-mail Notification 4 - Ship',
            'E-mail Notification 4 - Exception',
            'E-mail Notification 4 - Delivery',
            'E-mail Notification 5 - Address',
            'E-mail Notification 5 - Ship',
            'E-mail Notification 5 - Exception',
            'E-mail Notification 5 - Delivery',
            'E-Mail Message',
            'E-mail Failure Address',
            'UPS Premium Care',
            'Location ID',
            'Media Type',
            'Language',
            'Notification Address',
            'ADL COD Value',
            'ADL Deliver to Addressee',
            'ADL Shipper Media Type',
            'ADL Shipper Language',
            'ADL Shipper Notification'
        ];
    }
}

include __DIR__ . '/../public/init.php';

$job = new OrderToWorldshipJob();
$job->run($argv);
