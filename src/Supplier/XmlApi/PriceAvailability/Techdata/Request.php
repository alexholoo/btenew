<?php

namespace Supplier\XmlApi\PriceAvailability\Techdata;

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
        $username = $this->config['username'];
        $password = $this->config['password'];

        $lines = array();

        $lines[] = "<XML_PriceAvailability_Submit>";
        $lines[] = $this->header();
        $lines[] = $this->detail();
        $lines[] = "</XML_PriceAvailability_Submit>";

        return implode("\n", $lines);
    }

    protected function header()
    {
        $username = $this->config['username'];
        $password = $this->config['password'];

        $lines = array();

        $lines[] = "<Header>";
        $lines[] = "  <UserName>$username</UserName>";
        $lines[] = "  <Password>$password</Password>";
        $lines[] = "  <ResponseVersion>1.4</ResponseVersion>";
        $lines[] = "</Header>";

        return implode("\n", $lines);
    }

    protected function detail()
    {
        $lines = array();

        $lines[] = "<Detail>";
        foreach ($this->partnums as $partnum) {
            if (substr($partnum, 0, 3) == 'TD-') {
                $partnum = substr($partnum, 3);
            }

            $lines[] = "  <LineInfo>";
            $lines[] = "    <RefIDQual>VP</RefIDQual>";
            $lines[] = "    <RefID>$partnum</RefID>";
            $lines[] = "  </LineInfo>";
        }
        $lines[] = "</Detail>";

        return implode("\n", $lines);
    }
}
