<?php

namespace Supplier\XmlApi\PriceAvailability\Ingram;

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
        if (substr($partnum, 0, 4) == 'ING-') {
            $partnum = substr($partnum, 4);
        }

        $this->partnums[] = $partnum;
    }

    public function toXml()
    {
        $username = $this->config['username'];
        $password = $this->config['password'];

        $lines = array();

        $lines[] = "<PNARequest>";
        $lines[] = "<Version>2.0</Version>";
        $lines[] = $this->header();
        $lines[] = $this->partnumList();
        $lines[] = "<ShowDetail>2</ShowDetail>";
        $lines[] = "</PNARequest>";

        return implode("\n", $lines);
    }

    protected function header()
    {
        $username = $this->config['username'];
        $password = $this->config['password'];

        $lines = array();

        $lines[] = "<TransactionHeader>";
        $lines[] = "  <SenderID>ME</SenderID>";          // ??
        $lines[] = "  <ReceiverID>YOU</ReceiverID>";     // ??
        $lines[] = "  <CountryCode>CA</CountryCode>";    // ??
        $lines[] = "  <LoginID>$username</LoginID>";
        $lines[] = "  <Password>$password</Password>";
        $lines[] = "  <TransactionID>1</TransactionID>"; // ??
        $lines[] = "</TransactionHeader>";

        return implode("\n", $lines);
    }

    protected function partnumList()
    {
        $lines = array();

        foreach ($this->partnums as $partnum) {
            $lines[] = '<PNAInformation SKU="'. $partnum . '" Quantity="" />';
        }

        return implode("\n", $lines);
    }
}