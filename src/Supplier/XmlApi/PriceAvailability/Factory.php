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
            $client = new \Supplier\XmlApi\PriceAvailability\DH\Client($this->config); // [$supplier]?
            break;

        case 'SYN':
        case 'SYNNEX':
            $client = new \Supplier\XmlApi\PriceAvailability\Synnex\Client($this->config); // [$supplier]?
            break;

        case 'ING':
        case 'INGRAM':
            $client = new \Supplier\XmlApi\PriceAvailability\Ingram\Client($this->config); // [$supplier]?
            break;

        case 'TD':
        case 'Techdata':
            $client = new \Supplier\XmlApi\PriceAvailability\Techdata\Client($this->config); // [$supplier]?
            break;

        case 'AS':
        case 'ASI':
            $client = new \Supplier\XmlApi\PriceAvailability\ASI\Client($this->config); // [$supplier]?
            break;

        default:
            throw \Exception('Unknown supplier ID: ' . $supplier);
            break;
        }

        return $client;
    }
}
