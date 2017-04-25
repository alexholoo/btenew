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
    public function parse()
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

        if ($xml->Header->OrderConfirmation) {
            foreach ($xml->Header->OrderConfirmation->OrderDetail->RefInfo as $RefInfo) {
                if ($RefInfo->RefIDQual2 == 'PO') {
                    $result->poNum = strval($RefInfo->RefID2);
                }

                if ($RefInfo->RefIDQual2 == 'ON') {
                    $result->orderNo = strval($RefInfo->RefID2);
                }

                if ($RefInfo->RefIDQual2 == 'IN') {
                    $result->invoice = strval($RefInfo->RefID2);
                }
            }
        }

        return $result;
    }
}
