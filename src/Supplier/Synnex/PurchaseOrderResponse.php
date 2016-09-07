<?php

namespace Supplier\Synnex;

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

        $result->status = strval($xml->OrderResponse->Code);

        // fatal error (username/password/ip/xml error)
        if ($xml->OrderResponse->ErrorMessage) {
            $result->status = Response::STATUS_ERROR;
            $result->errorMessage = strval($xml->OrderResponse->ErrorMessage);
            $result->errorDetail = strval($xml->OrderResponse->ErrorDetail);
            return $result;
        }

        if ($xml->OrderResponse->Code == 'accepted') {
            $result->status = Response::STATUS_OK;
        }

        if ($xml->OrderResponse->Code == 'rejected') {
            $result->status = Response::STATUS_ERROR;
            $result->errorMessage = strval($xml->OrderResponse->Reason);
        }

        $item = $xml->OrderResponse->Items[0];

        $result->orderNo = strval($item->Item->OrderNumber);
        $result->orderType = strval($item->Item->OrderType);

        #shipFrom = strval($item->Item->ShipFromWarehouse);

        return $result;
    }
}
