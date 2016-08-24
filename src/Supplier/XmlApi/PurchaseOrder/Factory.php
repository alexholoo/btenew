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
            $client = \Supplier\XmlApi\PurchaseOrder\DH\Client($this->config)
            break;

        case 'SYN':
        case 'SYNNEX':
            $client = \Supplier\XmlApi\PurchaseOrder\Synnex\Client($this->config)
            break;

        case 'ING':
        case 'INGRAM':
            $client = \Supplier\XmlApi\PurchaseOrder\Ingram\Client($this->config)
            break;

        case 'TD':
        case 'Techdata':
            $client = \Supplier\XmlApi\PurchaseOrder\Techdata\Client($this->config)
            break;

        case 'AS':
        case 'ASI':
            $client = \Supplier\XmlApi\PurchaseOrder\ASI\Client($this->config)
            break;

        default:
            throw \Exception('Unknown supplier ID: ' . $supplier);
            break;
        }

        return $client;
    }
}

