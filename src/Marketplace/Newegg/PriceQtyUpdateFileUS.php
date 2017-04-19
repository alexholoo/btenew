<?php

namespace Marketplace\Newegg;

class PriceQtyUpdateFileUS
{
    protected $filename;
    protected $delimiter = ",";
    protected $handle;
    protected $columns  = ['sku', 'price', 'quantity'];

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function __destruct()
    {
        $this->close();
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
        return $this;
    }

    public function setColumns($columns)
    {
        $this->columns = $columns;
        return $this;
    }

    public function close()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

    public function write($data)
    {
        if (!$this->handle) {
            if (file_exists($this->filename)) {
                $this->handle = fopen($this->filename, 'a');
            } else {
                $this->handle = fopen($this->filename, 'w');
                if ($this->columns) {
                    fputcsv($this->handle, $this->columns, $this->delimiter);
                }
            }
        }

        if (count($data) != count($this->columns)) {
            throw new \Exception(get_called_class().': Wrong number of elements: '. var_export($data, true));
        }

        return fputcsv($this->handle, $data, $this->delimiter);
    }
}
