<?php

namespace Supplier\Synnex;

use Supplier\Model\PriceAvailabilityRequest as BaseRequest;

class PriceAvailabilityRequest extends BaseRequest
{
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

        return Utils::formatXml(implode("\n", $lines));
    }

    /**
     * @return string
     */
    protected function makePartnumList()
    {
        $lines = array();

        foreach ($this->partnums as $i => $partnum) {
            if (substr($partnum, 0, 4) == 'SYN-') {
                $partnum = substr($partnum, 4);
            }

            $lines[] = '<skuList>';
            $lines[] =   "<synnexSKU>$partnum</synnexSKU>";
            $lines[] =   "<lineNumber>".($i+1)."</lineNumber>";
            $lines[] = '</skuList>';
        }

        return implode("\n", $lines);
    }
}
