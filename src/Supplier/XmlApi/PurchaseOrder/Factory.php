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
        case \Supplier\Prefix::DH:
            $config = $this->config['xmlapi'][\Supplier\ConfigKey::DH];
            $client = new \Supplier\XmlApi\PurchaseOrder\DH\Client($config);
            break;

        case \Supplier\Prefix::SYNNEX:
            $client = new \Supplier\XmlApi\PurchaseOrder\Synnex\Client($config);
            break;

        case \Supplier\Prefix::INGRAM:
            $client = new \Supplier\XmlApi\PurchaseOrder\Ingram\Client($config);
            break;

        case \Supplier\Prefix::TECHDATA:
            $client = new \Supplier\XmlApi\PurchaseOrder\Techdata\Client($config);
            break;

        case \Supplier\Prefix::ASI:
            $client = new \Supplier\XmlApi\PurchaseOrder\ASI\Client($config);
            break;

        default:
            throw \Exception('Unknown supplier ID: ' . $supplier);
            break;
        }

        return $client;
    }
}

