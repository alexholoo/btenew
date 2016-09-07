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

        $result = new PriceAvailabilityResult();

        if ($xml->error) {
            $result->status = Response::STATUS_ERROR;
            $result->errorMessage = $xml->error->message;
            return $result;
        }

        $item = new PriceAvailabilityItem();

        $item->sku    = 'AS-'. strval($xml->Inventory['SKU']);
        $item->price  = strval($xml->Inventory->Price);
        $item->status = strval($xml->Inventory->Status);

        foreach($xml->Inventory->Qty->Branch as $branch) {
            $item->avail[] = [
                'branch' => strval($branch['Name']),
                'qty'    => strval($branch),
            ];
        }

        $result->add($item);
        $result->status = Response::STATUS_OK;

        return $result;
    }
}
