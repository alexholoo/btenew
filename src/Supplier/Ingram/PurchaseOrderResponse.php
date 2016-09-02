<?php

namespace Supplier\Ingram;

use Supplier\Model\PurchaseOrderResponse as BaseResponse;

class PurchaseOrderResponse extends BaseResponse
{
    /**
     * @return array
     */
    public function parseXml()
    {
        $xml = simplexml_load_string($this->xmldoc);

        $this->orders['OrderNo'] = strval($xml->OrderInfo->OrderNumbers->BranchOrderNumber);

        $this->status = strval($xml->TransactionHeader->ErrorStatus['ErrorNumber']);
        $this->errorMessage = strval($xml->TransactionHeader->ErrorStatus);

        return $this->orders;
    }
}
