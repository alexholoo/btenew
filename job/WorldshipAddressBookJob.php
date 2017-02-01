<?php

include 'classes/Job.php';

use Toolkit\Utils;
use Toolkit\AmericaState;

class WorldshipAddressBookJob extends Job
{
    public function run($args = [])
    {
        $this->log('>> '. __CLASS__);

        $this->generateAddressBook();
    }

    protected function generateAddressBook()
    {
        $orders = $this->getOrders();

        $filename = 'w:/out/shipping/UPS/worldship.csv';
        $out = fopen($filename, 'w');

        $title = $this->getAddressBookTitle();
        fputcsv($out, $title); // title is required

        foreach ($orders as $order) {
            /*
            Array
            (
                [date] => 2017-01-09
                [order_id] => 110-3538471-9940205
                [sku] => AS-172053
                [price] => 136.64
                [qty] => 1
                [product_name] => Gigabyte LGA1151 Intel Z170 Micro ATX DDR4 Motherboards GA-Z170M-D3H
                [buyer] => Emre Cengiz
                [address] => 21690 Ravine Rd
                [city] => Lake Zurich
                [province] => IL
                [postalcode] => 60047
                [country] => US
                [phone] => 7086686452
                [email] =>
            )
            */

            $orderId    = $order['order_id'];
            $buyer      = $order['buyer'];
            $address    = trim($order['address']);
            $city       = $order['city'];
            $province   = AmericaState::nameToCode($order['province']);
            $postalcode = $order['postalcode'];
            $country    = $order['country'];
            $phone      = Utils::formatPhoneNumber($order['phone']);
            $sku        = $order['sku'];
           #$price      = $order['price'];
           #$qty        = $order['qty'];
            $product    = $order['product_name'];

            $data = array_combine($title, array_fill(0, count($title), ''));

            $arr = explode("\n", wordwrap($address, 35, "\n"));
            $addr1 = $arr[0];
            $addr2 = isset($arr[1]) ? $arr[1] : '';

            $data["ConsigneeName"]  = $buyer;
            $data["ConsigneeID"]    = $orderId;
            $data["Address"]        = $addr1;
            $data["Address2"]       = $addr2;
            $data["City"]           = $city;
            $data["Province"]       = $province;
            $data["CountryCode"]    = $country;
            $data["PostalCode"]     = $postalcode;
            $data["ConsigneePhone"] = $phone;
            $data["ContactName"]    = $buyer;
            $data["ContactEmail"]   = '';
            $data["Instruction"]    = substr($orderId, -7);
            $data["Reference"]      = $orderId;
            $data["Description"]    = $product;
            $data["SKU"]            = $sku;
            $data["Weight"]         = $this->getWeight($sku);

            fputcsv($out, $data);
        }

        fclose($out);

        $this->log(count($orders). " orders imported to $filename");
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

        $filename = 'w:/out/shipping/UPS/worldship.xml';
        file_put_contents($filename, implode("\n", $lines));

        $this->log(count($orders). " orders imported to $filename");
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
        $province   = AmericaState::nameToCode($order[8]);
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
        $start = date('Y-m-d', strtotime('-30 days'));

        $sql = "SELECT o.date, oi.order_id, oi.sku, oi.price, oi.qty, oi.product_name,
                       sa.buyer, sa.address, sa.city, sa.province, sa.postalcode,
                       sa.country, sa.phone, sa.email
                  FROM master_order o
                  JOIN master_order_item oi ON o.order_id = oi.order_id
                  JOIN master_order_shipping_address sa ON sa.order_id = o.order_id
                 WHERE o.channel = 'Amazon-US' and o.date >= '$start'";

        $orders = [];

        $result = $this->db->fetchAll($sql);

        foreach ($result as $order) {
            $orders[] = $order;
        }

        return $orders;
    }

    protected function getOrders_FROM_FILE()
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

        $start = date('Y-m-d', strtotime('-7 days'));

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
        $result = $this->getMasterSku($sku);

        return isset($result['Weight']) ? $result['Weight'] : '';
    }

    protected function getAddressBookTitle()
    {
        return [
            "ConsigneeName",
            "ConsigneeID",
            "Address",
            "Address2",
            "City",
            "Province",
            "CountryCode",
            "PostalCode",
            "ConsigneePhone",
            "ContactName",
            "ContactEmail",
            "Instruction",
            "Reference",
            "Description",
            "SKU",
            "Weight",
        ];
    }
}

include __DIR__ . '/../public/init.php';

$job = new WorldshipAddressBookJob();
$job->run($argv);
