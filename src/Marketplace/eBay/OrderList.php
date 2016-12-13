<?php

namespace Marketplace\eBay;

use Toolkit\CsvFileWriter;
use Toolkit\CsvFileReader;

class OrderList
{
    protected $channel;

    protected $csvWriter;
    protected $csvReader;

    protected $filename = "E:/BTE/orders/ebay/ebay_orders_bte.csv";

    protected $csvTitle = array(
        //
    );

    public function __construct($site)
    {
        $this->channel = 'Amazon-US';
        $this->csvReader = new CsvFileReader($this->filename, true);
    }

    public function getStdFields()
    {
        $fields = $this->csvReader->read();
        if (!$fields) {
            return [];
        }

        $channel = 'ebay';

        $eb_date_string = strtotime($fields[3]);
        $date = date('Y-m-d',$eb_date_string);
        $today = date('Y-m-d');
        if (strtotime ($today) > (strtotime($date) + $order_period) ) { // past 7 days
            return;
        }

        $order_id = $fields[0];
        $mgn_order_id = '';
        $express_info = $fields[6];

        $express = 0;
        if ((strpos($express_info,'Expedited'))==true) {
             $express = 1;
        }

        $full_name = $fields[8] ;
        $full_adress = $fields[9] . ' ' . $fields[10];

        if($fields [14]=='Canada'){
            $country='CA';
        }
        if($fields [14]=='United States'){
            $country='US';
        }
        if($fields [14]=='Germany'){
            $country='DE';
        }

        return array(
            $channel,
            $date,
            $order_id,
            $mgn_order_id,
            $express,
            $full_name,//full name
            $full_adress,//address
            $fields [11],//city'
            $fields [12],//province'
            $fields [13],//postalcode
            $country,
            $fields [15],//phone'
            $fields [17],//email'
            $fields [18],//skus_sold'
            $fields [20],//sku_price'
            $fields [16],//skus_qty'
            $fields [7],//shipping cost
            'n/a',
        ); //Transaction ID
    }
}
