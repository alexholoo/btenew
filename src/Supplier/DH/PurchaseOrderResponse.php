<?php

namespace Supplier\DH;

use Supplier\Model\PurchaseOrderResponse as BaseResponse;

class PurchaseOrderResponse extends BaseResponse
{
    /**
     * @return array
     */
    public function parseXml()
    {
        $xml = simplexml_load_string($this->xmldoc);

        $this->status = strval($xml->STATUS);
        $this->orders = strval($xml->ORDERNUM);
        $this->errorMessage = strval($xml->MESSAGE);

        return $this->orders;
    }
}
