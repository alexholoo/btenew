<?php

namespace Marketplace\Newegg;

use Toolkit\CsvFileWriter;
use Toolkit\CsvFileReader;

class MasterOrderList
{
    protected $site;
    protected $filename;

    public function __construct($site = 'CA')
    {
        $this->site = strtoupper($site);

        if ($this->site == 'CA') {
            $this->filename = "E:/BTE/orders/newegg/orders_ca/newegg_ca_master_orders.csv";
        }

        if ($this->site == 'US') {
            $this->filename = "E:/BTE/orders/newegg/orders_us/newegg_us_master_orders.csv";
        }
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function generate()
    {
        if ($this->site == 'CA') {
            $files = glob("E:/BTE/orders/newegg/orders_ca/OrderList_*.*");
        }

        if ($this->site == 'US') {
            $files = glob("E:/BTE/orders/newegg/orders_us/OrderList_*.*");
        }

        $out = fopen($this->filename, "w");
        fwrite($out, OrderList::getTitleString());

        foreach($files as $file){
            $datetime = strtotime(preg_replace('/[^0-9]/', '', $file));
            if ((time() - $datetime) / (3600*24) > 30) { // 30 days old
                continue;
            }

            $in = fopen($file, "r");
            fgets($in); // skip title row
            while (($line = fgets($in)) !== false) {
                fwrite($out, $line);
            }
            fclose($in);
        }

        fclose($out);
    }

    public function generateAddress()
    {
        if ($this->site == 'CA') {
            $addressFile = "E:/BTE/orders/newegg/orders_ca/newegg_ca_address.csv";
        }

        if ($this->site == 'US') {
            $addressFile = "E:/BTE/orders/newegg/orders_us/newegg_us_address.csv";
        }

        $title = array(
            "orderID",
            "name",
            "company",
            "address1",
            "address2",
            "city",
            "province",
            "postal_code",
            "country" ,
            "phone",
            "email",
            "shippingmethod",
            "sku",
            "price",
            "shipping",
            "qty",
            "date"
        );

        $address = fopen($addressFile, 'w');
        fputcsv($address, $title);

        $orders = fopen($this->filename, 'r');
        fgetcsv($orders); // skip the title row

        while (($fields = fgetcsv($orders))) {
            if (!is_numeric($fields[0])) {
                continue;
            }

            $orderID    = $fields[0];
            $orderDate  = $fields[1];
            $name       = $fields[10]. ' '. $fields[11];
            $company    = $fields[12];
            $address1   = $fields[4];
            $address2   = $fields[5];
            $city       = $fields[6];
            $province   = $fields[7];
            $postalcode = $fields[8];
            $country    = 'CA';// $fields[9];
            $phone      = $fields[13];
            $email      = $fields[14];
            $shipping   = $fields[15];

            if ($shipping =='Standard Shipping (5-7 business days)') {
                $shippingMethod = 'Standard'; // canada post expedited shipping
            } else if ($shipping =='Expedited Shipping (3-5 business days)') {
                $shippingMethod = 'Expedited'; // canada post express shipping
            } else if ($shipping =='Two-Day Shipping') {
                $shippingMethod = 'Piority';
            }

            $sku      = $fields[16];
            $price    = $fields[18];
            $shipping = $fields[20];
            $qty      = $fields[26];

            $data = array(
                $orderID,
                $name,
                $company,
                $address1,
                $address2,
                $city,
                $province,
                $postalcode,
                $country,
                $phone,
                $email,
                $shippingMethod,
                $sku,
                $price,
                $shipping,
                $qty,
                $orderDate
            );

            fputcsv($address, $data);
        }

        fclose ($orders);
        fclose ($address);
    }

    // TODO: refactor, not done yet
    public function getStdFields()
    {
        while (($fields = fgetcsv($newegg)))
        {
            $channel = 'NeweggCA';
            $channel = 'NeweggUSA';

            $ng_date_string = strtotime($fields [1]);
            $date = date('Y-m-d', $ng_date_string);
            $today = date('Y-m-d');
            if (strtotime($today) > ($ng_date_string + $order_period)) {
                continue;
            }

            $order_id = $fields [0];
            $mgn_order_id = '';
            $express_info = $fields [15];

            $express = 0;
            if ($express_info == 'Expedited Shipping (3-5 business days)') {
                $express = 1;
            }
            else if ($express_info == 'Two-Day Shipping') {
                $express = 1;
                $order_id = 'NextDay-'.$order_id;
            }

            $full_name = $fields[10]. ' ' .$fields[11];
            $full_address = $fields[4]. ' ' .$fields[5];

            $order_array = array(
                $channel,
                $date,
                $order_id,
                $mgn_order_id,
                $express,
                $full_name,//full name
                $full_address,//address
                $fields [6],//city'
                $fields [7],//province'
                $fields [8],//postalcode
                'CA',//country'
                $fields [13],//phone'
                $fields [14],//email'
                $fields [16],//skus_sold'
                $fields [18],//sku_price'
                $fields [26],//skus_qty'
                $fields [20],//shipping'
                'n/a' //invoice id is null for newegg orders
            );

            fputcsv($mgn, $order_array);
        }
	}
}
