<?php

include __DIR__ . '/../public/init.php';

use Toolkit\Utils;
use Toolkit\AmericaState;
use Toolkit\CanadaProvince;

class WorldshipAddressBookJob extends Job
{
    public function run($args = [])
    {
        $this->log('>> '. __CLASS__);

        $this->skuService = $this->di->get('skuService');

        $this->generateAddressBook();
    }

    protected function generateAddressBook()
    {
        $orders = $this->getUnshippedOrders();

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
            $postalcode = $order['postalcode'];
            $country    = $order['country'];
            $phone      = Utils::formatPhoneNumber($order['phone']);
            $sku        = $order['sku'];
           #$price      = $order['price'];
           #$qty        = $order['qty'];
            $product    = $order['product_name'];

            if ($order['country'] == 'US') {
                $province = AmericaState::nameToCode($order['province']);
            }

            if ($order['country'] == 'CA') {
                $province = CanadaProvince::nameToCode($order['province']);
            }

            $data = array_combine($title, array_fill(0, count($title), ''));

            $arr = explode("\n", wordwrap($address, 35, "\n"));
            $addr1 = $arr[0];
            $addr2 = isset($arr[1]) ? $arr[1] : '';

            // Get last 7 digits for Amazon order, full orderId for non-Amazon order
            $parts = explode('-', $orderId);
            $searchKey = end($parts);

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
            $data["Instruction"]    = $searchKey;
            $data["Reference"]      = $orderId;
            $data["Description"]    = $product;
            $data["SKU"]            = $sku;
            $data["Weight"]         = $this->getWeight($sku);

            fputcsv($out, $data);
        }

        fclose($out);

        $this->log(count($orders). " orders imported to $filename");
    }

    protected function getUnshippedOrders()
    {
        $start = date('Y-m-d', strtotime('-10 days'));

        $sql = "SELECT o.date,
                       oi.order_id,
                       oi.sku,
                       oi.price,
                       oi.qty,
                       oi.product_name,
                       sa.buyer,
                       sa.address,
                       sa.city,
                       sa.province,
                       sa.postalcode,
                       sa.country,
                       sa.phone,
                       sa.email
                  FROM master_order                   o
                  JOIN master_order_item             oi ON  o.order_id = oi.order_id
                  JOIN master_order_shipping_address sa ON sa.order_id = o.order_id
             LEFT JOIN master_order_shipped           s ON  o.order_id = s.order_id
                 WHERE o.date >= '$start' AND s.createdon IS NULL";

        $orders = [];

        $result = $this->db->fetchAll($sql);

        foreach ($result as $order) {
            $orders[] = $order;
        }

        return $orders;
    }

    protected function getWeight($sku)
    {
        return $this->skuService->getWeight($sku);
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

$job = new WorldshipAddressBookJob();
$job->run($argv);
