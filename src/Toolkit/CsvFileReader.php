<?php

namespace Toolkit;

class CsvFileReader
{
    protected $filename;
    protected $delimiter = ',';
    protected $hasHeadline = false;
    protected $headline = [];
    protected $columns = [];
    protected $handle;

    public function __construct($filename = '', $hasHeadline = false)
    {
        $this->filename = $filename;
        $this->hasHeadline = $hasHeadline;
    }

    public function __destruct()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    public function setHasHeadline($hasHeadline)
    {
        $this->hasHeadline = $hasHeadline;
        return $this;
    }

    public function setColumns($columns)
    {
        $this->columns = $columns;
        return $this;
    }

    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
        return $this;
    }

    public function getHeadline()
    {
        return $this->headerline;
    }

    public function read()
    {
        if (!$this->handle && file_exists($this->filename)) {
            $this->handle = fopen($this->filename, 'r');
            if ($this->hasHeadline) {
                $this->headline = fgetcsv($this->handle, 0, $this->delimiter);
            }
        }

        $fields = [];

        do {
            $fields = fgetcsv($this->handle, 0, $this->delimiter);
            if (!$fields) {
                return [];
            }
            // throw new \Exception('Wrong number of elements: '. var_export($fields, true));
        } while ($this->columns && count($fields) != count($this->columns));

        $fields = array_map('trim', $fields);

        if ($fields && $this->columns) {
            $fields = array_combine($this->columns, $fields);
        }

        return $fields;
    }
}

/*
sku,price,qty
SKU-111, 111.00, 11
"   SKU-222   ",      222.00,      22
SKU-333     , 333.00   , 33

SKU-AAA, aaa.00

SKU-444, 444.00, 44
*/
$csvfile = new CsvFileReader('e:/test.csv', true);
//$csvfile->setDelimiter("\t");
$csvfile->setColumns(array("SKU", "Price", "Qty"));
while ($fields = $csvfile->read()) {
    var_export($fields);
}
