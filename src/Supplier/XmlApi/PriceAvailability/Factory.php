<?php

namespace Supplier\XmlApi\PriceAvailability;

use Supplier\Prefix;
use Supplier\ConfigKey;

use Supplier\XmlApi\PriceAvailability\DH\Client as DHClient;
use Supplier\XmlApi\PriceAvailability\Synnex\Client as SynnexClient;
use Supplier\XmlApi\PriceAvailability\Ingram\Client as IngramClient;
use Supplier\XmlApi\PriceAvailability\Techdata\Client as TechdataClient;
use Supplier\XmlApi\PriceAvailability\ASI\Client as ASIClient;

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
            $client = new \Supplier\XmlApi\PriceAvailability\DH\Client($config);
            break;

        case \Supplier\Prefix::SYNNEX:
            $config = $this->config['xmlapi'][\Supplier\ConfigKey::SYNNEX];
            $client = new \Supplier\XmlApi\PriceAvailability\Synnex\Client($config);
            break;

        case \Supplier\Prefix::INGRAM:
            $config = $this->config['xmlapi'][\Supplier\ConfigKey::INGRAM];
            $client = new \Supplier\XmlApi\PriceAvailability\Ingram\Client($config);
            break;

        case \Supplier\Prefix::TECHDATA:
            $config = $this->config['xmlapi'][\Supplier\ConfigKey::TECHDATA];
            $client = new \Supplier\XmlApi\PriceAvailability\Techdata\Client($config);
            break;

        case \Supplier\Prefix::ASI:
            $config = $this->config['xmlapi'][\Supplier\ConfigKey::ASI];
            $client = new \Supplier\XmlApi\PriceAvailability\ASI\Client($config);
            break;

        default:
            throw \Exception('Unknown supplier ID: ' . $supplier);
            break;
        }

        return $client;
    }
}
