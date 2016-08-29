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
        $this->partnums[] = $partnum;
    }

    public function getPartnum()
    {
        return $this->partnums[0];
    }

    public function toXml()
    {
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
        $loginId = $this->config['loginId'];
        $password = $this->config['password'];

        $lines = array();

        $lines[] = "<TransactionHeader>";
        $lines[] = "  <SenderID>ME</SenderID>";          // ??
        $lines[] = "  <ReceiverID>YOU</ReceiverID>";     // ??
        $lines[] = "  <CountryCode>CA</CountryCode>";    // ??
        $lines[] = "  <LoginID>$loginId</LoginID>";
        $lines[] = "  <Password>$password</Password>";
        $lines[] = "  <TransactionID>1</TransactionID>"; // ??
        $lines[] = "</TransactionHeader>";

        return implode("\n", $lines);
    }

    protected function partnumList()
    {
        $lines = array();

        foreach ($this->partnums as $partnum) {
            if (substr($partnum, 0, 4) == 'ING-') {
                $partnum = substr($partnum, 4);
            }

            $lines[] = '<PNAInformation SKU="'. $partnum . '" Quantity="" />';
        }

        return implode("\n", $lines);
    }
}
