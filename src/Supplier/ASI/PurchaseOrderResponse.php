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
    public function parse()
    {
        $xml = simplexml_load_string($this->xmldoc);

        $result = new PurchaseOrderResult();

        if ($xml->error) {
            $result->status = Response::STATUS_ERROR;
            $result->errorMessage = strval($xml->error->message);
            return $result;
        }

        if ($xml->order) {
            $result->status = Response::STATUS_OK;
            $result->orderNo = strval($xml->order->orderid);
        }

        return $result;
    }
}
