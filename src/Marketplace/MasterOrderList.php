<?php

namespace Marketplace;

use Toolkit\CsvFileWriter;
use Toolkit\CsvFileReader;

class MasterOrderList
{
    protected $csvWriter;
    protected $csvReader;

    protected $csvTitle = array(
        'channel',
        'date',
        'channel_order_id',
        'mgn_order_id',
        'express?',
        'buyer',
        'address',
        'city',
        'province',
        'postalcode',
        'country',
        'phone',
        'email',
        'skus_sold',
        'sku_price',
        'skus_qty',
        'shipping',
        'mgn_invoice_id'
    );

    public function __construct()
    {
        $filename = 'E:/BTE/orders/all_mgn_orders.csv';

        $csvWriter = new CsvFileWriter($filename, $this->csvTitle);
        $csvWriter->setFilemode(CsvFileWriter::MODE_CREATE);

        $csvReader = new CsvFileReader($filename, true);
        $csvReader->setColumns($this->csvTitle);
    }

    public function read()
    {
        return $this->csvReader->read();
    }

    public function write($data)
    {
        return $this->csvWriter->write($data);
    }
}
