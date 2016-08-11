<?php

namespace Supplier\XmlApi\DH\PriceAvailability;

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

    public function toXml()
    {
        $username = $this->config['username'];
        $password = $this->config['password'];

        $lines = array();

        $lines[] = "<XMLFORMPOST>";
        $lines[] =   "<REQUEST>price-availability</REQUEST>";
        $lines[] =   "<LOGIN>";
        $lines[] =   "<USERID>$username</USERID>";
        $lines[] =   "<PASSWORD>$password</PASSWORD>";
        $lines[] =   "</LOGIN>";
        $lines[] =   $this->makePartnumList();
        $lines[] = "</XMLFORMPOST>";

        $xmldoc = implode("\n", $lines);

        return "xmlDoc=" . $xmldoc;
        return "xmlDoc=" . rawurlencode($xmldoc);
    }

    protected function makePartnumList()
    {
        $lines = array();
	
        foreach ($this->partnums as $partnum) {
            $lines[] = "<PARTNUM>$partnum</PARTNUM>";
        }

        return implode("\n", $lines);
    }
}
