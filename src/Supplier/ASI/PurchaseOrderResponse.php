<?php

namespace Supplier\ASI;

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

        $result->status = ''; // strval($xml->STATUS);
        $result->orderNo = ''; // strval($xml->ORDERNUM);
        $result->errorMessage = ''; // strval($xml->MESSAGE);

        if ($result->status == 'success') {
            $result->status = Response::STATUS_OK;
        }

        if ($result->status == 'failure') {
            $result->status = Response::STATUS_ERROR;
        }

        return $result;
    }
}
