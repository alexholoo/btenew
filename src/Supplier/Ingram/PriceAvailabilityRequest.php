<?php

namespace Supplier\Ingram;

use Utility\Utils;
use Supplier\Model\PriceAvailabilityRequest as BaseRequest;

class PriceAvailabilityRequest extends BaseRequest
{
    /**
     * @return string
     */
    public function toXml()
    {
        $lines = array();

        $lines[] = "<PNARequest>";
        $lines[] = "<Version>2.0</Version>";
        $lines[] = $this->header();
        $lines[] = $this->partnumList();
        $lines[] = "<ShowDetail>2</ShowDetail>";
        $lines[] = "</PNARequest>";

        return Utils::formatXml(implode("\n", $lines));
    }

    /**
     * @return string
     */
    protected function header()
    {
        $loginId = $this->config['loginId'];
        $password = $this->config['password'];

        $lines = array();

        $lines[] = "<TransactionHeader>";
        $lines[] =   "<SenderID>ME</SenderID>";          // ME
        $lines[] =   "<ReceiverID>YOU</ReceiverID>";     // YOU
        $lines[] =   "<CountryCode>FT</CountryCode>";    // not CA
        $lines[] =   "<LoginID>$loginId</LoginID>";
        $lines[] =   "<Password>$password</Password>";
        $lines[] =   "<TransactionID>1</TransactionID>"; // ??
        $lines[] = "</TransactionHeader>";

        return implode("\n", $lines);
    }

    /**
     * @return string
     */
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
