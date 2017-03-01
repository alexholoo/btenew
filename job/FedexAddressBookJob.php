<?php

include 'classes/Job.php';

use Toolkit\Utils;
use Toolkit\AmericaState;
use Toolkit\CanadaProvince;

class FedexAddressBookJob extends Job
{
    public function run($args = [])
    {
        $this->log('>> '. __CLASS__);

        $this->skuService = $this->di->get('skuService');

        $this->generateAddressBook();
    }

    protected function generateAddressBook()
    {
        $orders = $this->getOrders();

        $filename = 'w:/out/shipping/tnt_shipment_import.csv';
        $filename = 'e:/tnt_shipment_import.csv';

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

            $sku      = $order['sku'];
            $phone    = Utils::formatPhoneNumber($order['phone']);
            $price    = $order['price'];
            $qty      = $order['qty'];
            $value    = $price * $qty;
            $info     = $this->getMasterSku($sku);

            if ($order['country'] == 'US') {
                $province = AmericaState::nameToCode($order['province']);
            }

            if ($order['country'] == 'CA') {
                $province = CanadaProvince::nameToCode($order['province']);
            }

            $data = [
                '1',
                $order['order_id'],
                '',
                $order['buyer'],
                $order['buyer'],
                '',
                '',
                '',
                trim($order['address']),
                '', // $address2,
                $order['city'],
                $province,
                $order['postalcode'],
                $order['country'],
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

    protected function getOrders()
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
                  FROM master_order_item oi
                  JOIN master_order o ON o.order_id = oi.order_id
                  JOIN master_order_shipping_address sa ON sa.order_id = o.order_id
                 WHERE (o.channel = 'Amazon-US' OR o.channel = 'Amazon-ACA') AND o.date >= '$start'";

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

include __DIR__ . '/../public/init.php';

$job = new FedexAddressBookJob();
$job->run($argv);
