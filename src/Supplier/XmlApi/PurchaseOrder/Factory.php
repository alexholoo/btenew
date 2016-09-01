<?php

namespace Supplier\XmlApi\PurchaseOrder;

use Supplier\Prefix;
use Supplier\ConfigKey;

use Supplier\XmlApi\PurchaseOrder\DH\Client as DHClient;
use Supplier\XmlApi\PurchaseOrder\Synnex\Client as SynnexClient;
use Supplier\XmlApi\PurchaseOrder\Ingram\Client as IngramClient;
use Supplier\XmlApi\PurchaseOrder\Techdata\Client as TechdataClient;
use Supplier\XmlApi\PurchaseOrder\ASI\Client as ASIClient;

class Factory
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function createClient($sku)
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
            $config = $this->config['xmlapi'][\Supplier\ConfigKey::SYNNEX];
            $client = new \Supplier\XmlApi\PurchaseOrder\Synnex\Client($config);
            break;

        case \Supplier\Prefix::INGRAM:
            $config = $this->config['xmlapi'][\Supplier\ConfigKey::INGRAM];
            $client = new \Supplier\XmlApi\PurchaseOrder\Ingram\Client($config);
            break;

        case \Supplier\Prefix::TECHDATA:
            $config = $this->config['xmlapi'][\Supplier\ConfigKey::TECHDATA];
            $client = new \Supplier\XmlApi\PurchaseOrder\Techdata\Client($config);
            break;

        case \Supplier\Prefix::ASI:
            #$config = $this->config['xmlapi'][\Supplier\ConfigKey::ASI];
            #$client = new \Supplier\XmlApi\PurchaseOrder\ASI\Client($config);
            #break;

        default:
            throw \Exception('Unknown supplier ID: ' . $supplier);
            break;
        }

        return $client;
    }
}

