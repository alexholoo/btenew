<?php

namespace Supplier\XmlApi\PriceAvailability\ASI;

class Request
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $partnums = array();

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function addPartnum($partnum)
    {
        $this->partnums[] = $partnum;
    }

    public function getPartnum()
    {
        return $this->partnums[0];
    }

    public function toXml()
    {
        $cid  = $this->config['CID'];
        $cert = $this->config['CERT'];
        $sku  = $this->getPartnum();

        if (substr($sku, 0, 3) == 'AS-') {
            $sku = substr($sku, 3);
        }

        return "?CID=$cid&CERT=$cert&SKU=$sku";
    }
}
