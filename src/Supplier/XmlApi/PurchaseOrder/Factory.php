<?php

namespace Supplier\XmlApi\PurchaseOrder;

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
            $client = new \Supplier\XmlApi\PurchaseOrder\DH\Client($this->config); // [$supplier]?
            break;

        case 'SYN':
        case 'SYNNEX':
            $client = new \Supplier\XmlApi\PurchaseOrder\Synnex\Client($this->config); // [$supplier]?
            break;

        case 'ING':
        case 'INGRAM':
            $client = new \Supplier\XmlApi\PurchaseOrder\Ingram\Client($this->config); // [$supplier]?
            break;

        case 'TD':
        case 'Techdata':
            $client = new \Supplier\XmlApi\PurchaseOrder\Techdata\Client($this->config); // [$supplier]?
            break;

        case 'AS':
        case 'ASI':
            $client = new \Supplier\XmlApi\PurchaseOrder\ASI\Client($this->config); // [$supplier]?
            break;

        default:
            throw \Exception('Unknown supplier ID: ' . $supplier);
            break;
        }

        return $client;
    }
}

