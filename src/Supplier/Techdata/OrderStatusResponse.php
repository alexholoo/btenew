<?php

namespace Supplier\Techdata;

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

        if ($xml->Detail->ErrorInfo) {
            $result->status = Response::STATUS_ERROR;
            $result->errorMessage = strval($xml->Detail->ErrorInfo->ErrorDesc);
            return $result;
        }

        $result->status = Response::STATUS_OK;

        foreach ($xml->Detail->RefInfo as $RefInfo) {
            if ($RefInfo->RefIDQual == 'PO') {
                $result->poNum = strval($RefInfo->RefID);
            }

            if ($RefInfo->RefIDQual == 'ON') {
                $result->orderNo = strval($RefInfo->RefID);
            }

            if ($RefInfo->RefIDQual == 'IN') {
                $result->invoice = strval($RefInfo->RefID);
            }
        }

        $result->sku = 'TD-'.strval($xml->Detail->ContainerInfo->ItemInfo->ProductID);
        $result->qty = strval($xml->Detail->ContainerInfo->ItemInfo->QtyShipped);
        $result->carrier = strval($xml->Detail->ContainerInfo->ShipVia);
       #$result->service = strval($xml->Detail->);
        $result->trackingNumber = strval($xml->Detail->ContainerInfo->ContainerID);
        $result->shipDate = $this->fmtdate(strval($xml->Detail->ContainerInfo->DateShipped));

        return $result;
    }

    private function fmtdate($date)
    {
        // YYMMDD => YYYYMMDD
        if (strlen($date) == 6) {
            list($y, $m, $d) = str_split($date, 2);
            return "20$y-$m-$d";
        }

        // 10/20/16 => 2016-10-20
        if (strlen($date) == 8) {
            list($m, $d, $y) = explode('/', $date);
            return "20$y-$m-$d";
        }

        return $date;
    }
}
