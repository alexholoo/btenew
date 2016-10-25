<?php

namespace Supplier\DH;

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

        $result->status = strval($xml->STATUS);
        $result->errorMessage = strval($xml->MESSAGE);

        if ($result->status == 'success') {
            $result->status = Response::STATUS_OK;
            $result->poNum = strval($xml->ORDERSTATUS->ORDERNUM);
            $result->orderNo = strval($xml->ORDERSTATUS->PONUM);
            $result->invoice = strval($xml->ORDERSTATUS->INVOICE);
            $result->sku = 'DH-'.strval($xml->ORDERSTATUS->ORDERDETAIL->DETAILITEM->ITEMNO);
            $result->qty = strval($xml->ORDERSTATUS->ORDERDETAIL->DETAILITEM->QUANTITY);
            $result->carrier = strval($xml->ORDERSTATUS->PACKAGE->CARRIER);
            $result->service = strval($xml->ORDERSTATUS->PACKAGE->SERVICE);
            $result->trackingNumber = strval($xml->ORDERSTATUS->PACKAGE->TRACKNUM);
            $result->shipDate = self::fmtdate(strval($xml->ORDERSTATUS->PACKAGE->DATESHIPPED));
        }

        if ($result->status == 'failure') {
            $result->status = Response::STATUS_ERROR;
        }

        return $result;
    }

    protected function fmtdate($date)
    {
        // 10/20/16 => 2016-10-20
        list($m, $d, $y) = explode('/', $date);
        return "20$y-$m-$d";
    }
}
