<?php

namespace Supplier\Techdata;

use Supplier\Model\Response;
use Supplier\Model\PriceAvailabilityItem;
use Supplier\Model\PriceAvailabilityResult;
use Supplier\Model\PriceAvailabilityResponse as BaseResponse;

class PriceAvailabilityResponse extends BaseResponse
{
    /**
     * @return Supplier\Model\PriceAvailabilityResult
     */
    public function parseXml()
    {
        $xml = simplexml_load_string($this->xmldoc);

        $result = new PriceAvailabilityResult();

        foreach ($xml->Detail->LineInfo as $x) {
            $item = new PriceAvailabilityItem();

            $item->sku = 'TD-' . strval($x->RefID1);

            if ($x->ErrorInfo) {
                $item->status = strval($x->ErrorInfo->ErrorDesc);
                continue;
            }

            $item->price  = strval($x->UnitPrice1); // ?
            $item->price1 = strval($x->UnitPrice1); // ?
            $item->price2 = strval($x->UnitPrice2); // ?
            $item->status = strval($x->ItemStatus);
            $item->weight = strval($x->ProductWeight);

            foreach ($x->WhseInfo as $branch) {
                #if ($branch->Qty == 0) {
                #    continue;
                #}

                $item->avail[] = [
                    'branch' => strval($branch->IDCode),
                    'qty'    => strval($branch->Qty),
                   #'code'   => strval($branch->WhseCode),
                ];

                if ($branch->TotalOnOrderQty) {
                   #$item->avail['TotalOnOrderQty'] = strval($branch->TotalOnOrderQty);
                }
                if ($branch->OnOrderQty) {
                   #$item->avail['OnOrderQty'] = strval($branch->OnOrderQty);
                }
                if ($branch->OnOrderETADate) {
                   #$item->avail['OnOrderETADate'] = strval($branch->OnOrderETADate);
                }
            }

            $result->add($item);
        }

        $result->status = Response::STATUS_OK;

        return $return;
    }
}
