<?php

namespace Supplier\Ingram;

use Supplier\Model\Response;
use Supplier\Model\OrderStatusResult;
use Supplier\Model\OrderStatusResponse as BaseResponse;

class OrderTrackingResponse extends BaseResponse
{
    /**
     * @return Supplier\Model\OrderStatusResult
     */
    public function parseXml()
    {
        $xml = simplexml_load_string($this->xmldoc);

        $result = new OrderStatusResult();

        // Fatal error: Invalid Inbound XML Document
        if (isset($xml['Number'])) {
            $result->status = Response::STATUS_ERROR;
            $result->errorMessage = strval($xml);
            return $result;
        }

        $result->status = strval($xml->TransactionHeader->ErrorStatus['ErrorNumber']);

        if (!empty($result->status)) {
            $result->status = Response::STATUS_ERROR;
            $result->errorMessage = strval($xml->TransactionHeader->ErrorStatus);
            return $result;
        }

        $result->status = Response::STATUS_OK;
        $result->poNum = strval($xml->CustomerPO);
        $result->orderNo = strval($xml->Order->BranchOrderNumber);
       #$result->invoice = '';
        $result->sku = 'ING-'.strval($xml->Order->Suffix->Package->Contents->ContentDetail->SKU);
        $result->qty = strval($xml->Order->Suffix->Package->Contents->ContentDetail->Quantity);
        $result->carrier = strval($xml->Order->Suffix->Carrier);
       #$result->service = strval($xml->Order->Suffix->Package->TrackingURL);
        $result->trackingNumber = strval($xml->Order->Suffix->Package['ID']);
        $result->shipDate = strval($xml->Order->Suffix->Package->ShipDate);

        $result->poNum = OrderNumberMapper::getRealOrderNo($result->poNum);

        $temp = $result->poNum;
        $result->poNum = $result->orderNo;
        $result->orderNo = $temp;

        return $result;
    }
}
