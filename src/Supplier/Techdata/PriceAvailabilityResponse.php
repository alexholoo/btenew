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

        $this->items = array();

        if ($xml->Detail->LineInfo->ErrorInfo) {
            $this->status = 'ERROR'; // ?
            $this->errorMessage = strval($xml->Detail->LineInfo->ErrorInfo->ErrorDesc);
            return $this->items;
        }

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
        foreach ($xml->Detail->LineInfo as $x) {
            if ($x->ErrorInfo) {
                //continue;
            }

            $item = [];
            $item['sku']   = 'TD-' . strval($x->RefID1);
            $item['price'] = strval($x->UnitPrice1); // $x->UnitPrice2;
            $item['avail'] = [];

            #$x->ProductWeight
            #$x->ItemStatus

            foreach ($x->WhseInfo as $branch) {
                if ($branch->Qty > 0) {
                    #$branch->WhseCode
                    $item['avail'][] = [
                        'branch' => strval($branch->IDCode),
                        'qty'    => strval($branch->Qty),
                    ];
                }
            }

            $this->items[] = $item;
        }

        $this->status = 'OK';

        return $this->items;
    }
}
