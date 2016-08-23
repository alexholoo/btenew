<?php

namespace Supplier\XmlApi\PriceAvailability\Synnex;

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

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param string $partnum
     */
    public function addPartnum($partnum)
    {
        $this->partnums[] = $partnum;
    }

    /**
     * @return string
     */
    public function toXml()
    {
        $customerNo = $this->config['customerNo'];
        $username   = $this->config['username'];
        $password   = $this->config['password'];

        $lines = array();
        $lines[] = '<?xml version="1.0" encoding="UTF-8" ?>';
        $lines[] = '<priceRequest>';
        $lines[] =   "<customerNo>$customerNo</customerNo>";
        $lines[] =   "<userName>$username</userName>";
        $lines[] =   "<password>$password</password>";
        $lines[] =   $this->makePartnumList();
        $lines[] = '</priceRequest>';

        return implode("\n", $lines);
    }

    /**
     * @return string
     */
    protected function makePartnumList()
    {
        $lines = array();

        foreach ($this->partnums as $i => $partnum) {
            $lines[] = '<skuList>';
            $lines[] =   "<synnexSKU>$partnum</synnexSKU>";
            $lines[] =   "<lineNumber>".($i+1)."</lineNumber>";
            $lines[] = '</skuList>';
        }

        return implode("\n", $lines);
    }
}
