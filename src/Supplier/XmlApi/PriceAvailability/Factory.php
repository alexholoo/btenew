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
            $client = \Supplier\XmlApi\PriceAvailability\DH\Client($this->config)
            break;

        case 'SYN':
        case 'SYNNEX':
            $client = \Supplier\XmlApi\PriceAvailability\Synnex\Client($this->config)
            break;

        case 'ING':
        case 'INGRAM':
            $client = \Supplier\XmlApi\PriceAvailability\Ingram\Client($this->config)
            break;

        case 'TD':
        case 'Techdata':
            $client = \Supplier\XmlApi\PriceAvailability\Techdata\Client($this->config)
            break;

        case 'AS':
        case 'ASI':
            $client = \Supplier\XmlApi\PriceAvailability\ASI\Client($this->config)
            break;

        default:
            throw \Exception('Unknown supplier ID: ' . $supplier);
            break;
        }

        return $client;
    }
}
