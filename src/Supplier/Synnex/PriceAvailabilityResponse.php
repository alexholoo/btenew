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

        foreach ($xml->PriceAvailabilityList as $x) {
            /**
             * $item = [
             *     'sku'   => '...',
             *     'price' => '...',
             *     'avail' => [
             *         [ 'branch' => 'BRANCH-1', 'qty' => 1 ],
             *         [ 'branch' => 'BRANCH-1', 'qty' => 2 ],
             *         [ 'branch' => 'BRANCH-1', 'qty' => 3 ],
             *     ]
             * ];
             */
            $item = [];
            $item['sku'] = 'SYN-' . strval($x->synnexSKU);
            $item['status'] = strval($x->status);
            $item['price'] = strval($x->price);
            $item['totalQty'] = strval($x->totalQuantity);
            $item['avail'] = [];

            #item['mfgPN'] = $x->mfgPN;
            #item['mfgCode'] = $x->mfgCode;
            #$x->description;
            #$x->GlobalProductStatusCode;

            $warehouses = $x->AvailabilityByWarehouse;
            foreach($warehouses as $warehouse) {
                $info = $warehouse->warehouseInfo;
                if ($warehouse->qty > 0) {
                    $item['avail'][] = [
                        'branch' => strval($info->city), // Warehouse::getName($info->number),
                        'qty'    => strval($warehouse->qty),
                    ];
                }

                #$info->zipcode;
                #$info->city;
                #$info->addr;
            }

            $this->items[] = $item;
        }

        return $this->items;
    }
}
