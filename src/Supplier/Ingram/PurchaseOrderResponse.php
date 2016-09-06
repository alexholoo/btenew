<?php

namespace Supplier\Ingram;

use Supplier\Model\PurchaseOrderResult;
use Supplier\Model\PurchaseOrderResponse as BaseResponse;

class PurchaseOrderResponse extends BaseResponse
{
    /**
     * @return Supplier\Model\PurchaseOrderResult
     */
    public function parseXml()
    {
        $xml = simplexml_load_string($this->xmldoc);

        $result = new PurchaseOrderResult();

        $result->orderNo = strval($xml->OrderInfo->OrderNumbers->BranchOrderNumber);
        $result->status = strval($xml->TransactionHeader->ErrorStatus['ErrorNumber']);
        $result->errorMessage = strval($xml->TransactionHeader->ErrorStatus);

        return $result;
    }
}
