<?php

namespace Supplier\ASI;

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

        if ($xml->error) {
            $this->status = 'ERROR';
            $this->errorMessage = $xml->error->message;
            return;
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
        $item = array(
            'sku'    => 'AS-'. strval($xml->Inventory['SKU']),
            'price'  => strval($xml->Inventory->Price),
            'avail'  => [ ],
            'status' => strval($xml->Inventory->Status),
        );

        foreach($xml->Inventory->Qty->Branch as $branch) {
            if ($branch > 0) {
                $item['avail'][] = [
                    'branch' => strval($branch['Name']),
                    'qty'    => strval($branch),
                ];
            }
        }

        $this->status = 'OK';

        $this->items[] = $item;

        return $this->items;
    }
}
