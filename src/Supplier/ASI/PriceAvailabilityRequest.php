<?php

namespace Supplier\ASI;

use Supplier\Model\PriceAvailabilityRequest as BaseRequest;

class PriceAvailabilityRequest extends BaseRequest
{
    /**
     * @return string
     */
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
