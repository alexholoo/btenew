<?php

namespace Marketplace\Amazon;

use Toolkit\CsvFileWriter;
use Toolkit\CsvFileReader;

class OrderList
{
    protected $channel;

    protected $csvWriter;
    protected $csvReader;

    protected $orderFileCA = 'E:/BTE/orders/amazon/amazon_ca_order_report.csv';
    protected $orderFileUS = 'E:/BTE/orders/amazon/amazon_us_order_report.csv';

    protected $csvTitle = array(
        //
    );

    public function __construct($site)
    {
        if ($site == 'CA') {
            $filename = $this->orderFileCA;
            $this->channel = 'Amazon-ACA';
        }

        if ($site == 'US') {
            $filename = $this->orderFileUS;
            $this->channel = 'Amazon-US';
        }

        $this->csvReader = new CsvFileReader($filename, true);
        $this->csvReader->setColumns($this->csvTitle);
    }

    public function getStdFields()
    {
        $fields = $this->csvReader->read();
        if (!$fields) {
            return [];
        }

        $date_string = strtotime($fields[2]);
        $date = date('Y-m-d', $date_string);

        //$today = date('Y-m-d');
        //if (strtotime($today) > ($date_string + $order_period)) {
        //    //echo $order_id. ' date is '.$date  .'older than 7 days, skipped <br>';
        //    continue;
        //}

        $order_id = $fields[0];
        $mgn_order_id = '';
        $express_info = $fields[15];

        $express = 0;
        if ($express_info == 'Expedited') {
             $express = 1;
        }

        $full_name = $fields[16] ;
        $full_adress = $fields[17]. ' ' .$fields[18]. ' ' .$fields[19];
        $unit_price = $fields[11] / $fields[9];

        return [
            $this->channel,
            $date,
            $order_id,
            $mgn_order_id,
            $express,
            $full_name,     // full name
            $full_adress,   // address
            $fields[20],    // city'
            $fields[21],    // province'
            $fields[22],    // postalcode
            $fields[23],    // country'
            $fields[24],    // phone'
            '',             // email'
            $fields[7],     // skus_sold'
            $unit_price,    // sku_price'
            $fields[9],     // skus_qty'
            $fields[13],    // shipping'
            'n/a',          // invoice id is null
        ];
    }
}
