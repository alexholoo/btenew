<?php

namespace Marketplace\Bestbuy;

use Toolkit\CsvFileReader;
use Toolkit\CsvFileWriter;

class OrderReportFile
{
    protected $filename;
    protected $fileReader;
    protected $fileWriter;

    public function __construct($filename)
    {
        $this->filename = $filename;

        $columns = [
            'date',
            'orderId',
            'sku',
            'price',
            'qty',
            'express',
            'state',
            'buyer',
            'address',
            'city',
            'state',
            'country',
            'zipcode',
            'phone',
        ];

        $this->fileReader = new CsvFileReader($filename);
        $this->fileWriter = new CsvFileWriter($filename);

        $this->fileReader->setHeadline($columns);
        $this->fileWriter->setHeadline($columns);
    }

    public function read()
    {
        return $this->fileReader->read();
    }

    public function write($order)
    {
        $this->fileWriter->write([
            $order['date'],
            $order['orderId'],
            $order['sku'],
            $order['price'],
            $order['qty'],
            $order['express'],
            $order['state'],
            $order['buyer'],
            $order['address'],
            $order['city'],
            $order['state'],
            $order['country'],
            $order['zipcode'],
            $order['phone'],
        ]);
    }
}
