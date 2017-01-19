<?php

namespace Marketplace\Amazon\Feeds;

class PriceAndQuantityUpdateFile
{
    protected $filename;
    protected $handle;
    protected $headline = ['sku', 'price', 'quantity'];
    protected $headline2 = [
        'sku',
        'price',
        'minimum-seller-allowed-price',
        'maximum-seller-allowed-price',
        'quantity',
        'leadtime-to-ship'
    ];

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
                if ($this->headline) {
                    fputcsv($this->handle, $this->headline, "\t");
                }
            }
        }

        if (count($data) != count($this->headline)) {
            throw new \Exception('Wrong number of elements: '. var_export($data, true));
        }

        return fputcsv($this->handle, $data, "\t");
    }
}
