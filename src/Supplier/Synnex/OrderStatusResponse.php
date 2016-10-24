<?php

namespace Supplier\Synnex;

use Supplier\Model\Response;
use Supplier\Model\OrderStatusResult;
use Supplier\Model\OrderStatusResponse as BaseResponse;

class OrderStatusResponse extends BaseResponse
{
    /**
     * @return Supplier\Model\OrderStatusResult
     */
    public function parseXml()
    {
        $xml = simplexml_load_string($this->xmldoc);

        $result = new OrderStatusResult();

        // fatal error (username/password/ip/xml error)
        if ($xml->OrderStatusResponse->ErrorMessage) {
            $result->status = Response::STATUS_ERROR;
            $result->errorMessage = strval($xml->OrderStatusResponse->ErrorMessage);
            $result->errorDetail = strval($xml->OrderStatusResponse->ErrorDetail);
            return $result;
        }

        $code = strval($xml->OrderStatusResponse->Code);

        if (in_array($code, ['shipped', 'invoiced'])) {
            $result->status = Response::STATUS_OK;
            $result->poNum = strval($xml->OrderStatusResponse->PONumber);
            $result->orderNo = strval($xml->OrderStatusResponse->Items->Item->OrderNumber);
           #$result->invoice = strval($xml->OrderStatusResponse->Items->Item->);
            $result->sku = 'SYN-'.strval($xml->OrderStatusResponse->Items->Item->SKU);
            $result->qty = strval($xml->OrderStatusResponse->Items->Item->OrderQuantity);
            $result->carrier = strval($xml->OrderStatusResponse->Items->Item->ShipMethod);
            $result->service = strval($xml->OrderStatusResponse->Items->Item->ShipMethodDescription);
            $result->trackingNumber = strval($xml->OrderStatusResponse->Items->Item->Packages->Package->TrackingNumber);
            $result->shipDate = strval($xml->OrderStatusResponse->Items->Item->ShipDatetime);
        }

        if (in_array($code, ['accepted', 'notFound', 'rejected', 'deleted'])) {
            $result->status = Response::STATUS_ERROR;
            $result->errorMessage = $code;
        }

        return $result;
    }
}
