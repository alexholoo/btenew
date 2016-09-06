<?php

namespace Supplier\Synnex;

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

        #$xml->customerNo
        #$xml->userName

        $result = new PriceAvailabilityResult();

        foreach ($xml->PriceAvailabilityList as $x) {
            $item = new PriceAvailabilityItem();

            $item->sku      = 'SYN-' . strval($x->synnexSKU);
            $item->price    = strval($x->price);
            $item->status   = strval($x->status);
            $item->totalQty = strval($x->totalQuantity);

            #$x->mfgPN;
            #$x->mfgCode;
            #$x->description;
            #$x->GlobalProductStatusCode;

            $warehouses = $x->AvailabilityByWarehouse;

            foreach($warehouses as $warehouse) {
                $info = $warehouse->warehouseInfo;

                #if ($warehouse->qty > 0) {
                $item->avail[] = [
                    'branch' => strval($info->city), // Warehouse::getName($info->number),
                    'qty'    => strval($warehouse->qty),
                ];
                #}

                #$info->zipcode;
                #$info->city;
                #$info->addr;
            }

            $result->add($item);
        }

        $result->status = Response::STATUS_OK;

        return $result;
    }
}
