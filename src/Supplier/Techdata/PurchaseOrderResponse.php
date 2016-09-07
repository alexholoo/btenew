<?php

namespace Supplier\Techdata;

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

        if ($xml->Header->RefID) {
            $result->status = Response::STATUS_OK;
            $result->orderNo = strval($xml->Header->RefID);
        }

        if ($xml->Header->DescHdrErr) {
            $result->status = Response::STATUS_ERROR;
            $result->errorMessage = strval($xml->Header->DescHdrErr);
        }

        return $result;
    }
}
