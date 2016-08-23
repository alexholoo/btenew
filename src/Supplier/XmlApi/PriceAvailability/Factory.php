<?php

namespace Supplier\XmlApi\PriceAvailability;

class Factory
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function create($sku)
    {
        $client = NULL;

        $parts = explode('-', $sku);
        $supplier = strtoupper($parts[0]);

        switch($supplier) {
        case 'DH':
            $client = DH\Client($this->config)
            break;
        case 'SYN':
        case 'SYNNEX':
            $client = Synnex\Client($this->config)
            break;
        default:
            throw \Exception('Unknown supplier ID: ' . $supplier);
            break;
        }

        return $client;
    }
}
