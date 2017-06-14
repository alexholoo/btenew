<?php

include __DIR__ . '/../public/init.php';

use Toolkit\Utils;
use Toolkit\AmericaState;
use Toolkit\CanadaProvince;

class FedexAddressBookJob extends Job
{
    public function run($args = [])
    {
        $this->log('>> '. __CLASS__);

        $this->generateAddressBook();
    }

    protected function generateAddressBook()
    {
        $orders = $this->getUnshippedOrders();

        $filename = 'w:/out/shipping/tnt_shipment_import.csv';

        $out = fopen($filename, 'w');
        stream_filter_append($out, "newlines");

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

            $sku   = $order['sku'];
            $phone = Utils::formatPhoneNumber($order['phone']);
            $price = $order['price'];
            $qty   = $order['qty'];
            $value = $price * $qty;
            $info  = $this->getMasterSku($sku);

            // To avoid fedex bug on tracking csv exporting
            $buyer   = str_replace(',', '', $order['buyer']);
            $address = str_replace(',', '', trim($order['address']));
            $city    = str_replace(',', '', trim($order['city']));

            // convert country and province/state to code
            $province = $order['province'];
            $country = $order['country'];

            if ($country == 'US' || strtoupper($country) == 'UNITED STATES') {
                $country = 'US';
                $province = AmericaState::nameToCode($province);
            }

            if ($country == 'CA' || strtoupper($country) == 'CANADA') {
                $country = 'CA';
                $province = CanadaProvince::nameToCode($province);
            }

            $data = [
                '1',
                $order['order_id'],
                '',
                $buyer,
                $buyer,
                '',
                '',
                '',
                $address,
                '', // $address2,
                $city,
                $province,
                $order['postalcode'],
                $country,
                $phone,
                '',
                '',
                '',
                '',
                $value,
                $info['Weight'],
                $info['Width'],
                $info['Length'],
                $info['Depth'],
            ];

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
                  FROM master_order_item             oi
                  JOIN master_order                   o ON  o.order_id = oi.order_id
                  JOIN master_order_shipping_address sa ON sa.order_id = o.order_id
             LEFT JOIN master_order_shipped           s ON oi.order_id = s.order_id
                 WHERE o.date >= '$start' AND s.createdon IS NULL";

        $result = $this->db->fetchAll($sql);
        return $result;
    }
}

class StreamFilterNewlines extends php_user_filter
{
    function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $bucket->data = preg_replace('/([^\r])\n/', "$1\r\n", $bucket->data);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }
        return PSFS_PASS_ON;
    }
}

stream_filter_register("newlines", "StreamFilterNewlines");

$job = new FedexAddressBookJob();
$job->run($argv);
