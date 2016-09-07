<?php

namespace Supplier\Ingram;

use Supplier\Model\Response;
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

        $result->status = Response::STATUS_OK;

        $errnum = strval($xml->TransactionHeader->ErrorStatus['ErrorNumber']);
        if (!empty($errnum)) {
            $result->status = Response::STATUS_ERROR;
            $result->errorMessage = strval($xml->TransactionHeader->ErrorStatus);
        }

        $result->orderNo = strval($xml->OrderInfo->OrderNumbers->BranchOrderNumber);

        return $result;
    }
}
