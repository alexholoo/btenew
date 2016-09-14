<?php

namespace Supplier\Ingram;

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

        $result->status = strval($xml->TransactionHeader->ErrorStatus['ErrorNumber']);

        if (!empty($result->status)) {
            $result->status = Response::STATUS_ERROR;
            $result->errorMessage = strval($xml->TransactionHeader->ErrorStatus);
            return $result;
        }

        foreach ($xml->PriceAndAvailability as $x) {
            $item = new PriceAvailabilityItem();

            $item->sku   = 'ING-'.strval($x['SKU']);
            $item->price = strval($x->Price);

            foreach ($x->Branch as $branch) {
                #if ($branch->Availability > 0) {
                $item->avail[] = [
                    'branch'  => strval($branch['Name']),
                    'code'    => strval($branch['ID']),
                    'qty'     => strval($branch->Availability),
                   #'OnOrder' => strval($branch->OnOrder),
                   #'ETADate' => strval($branch->ETADate),
                ];
                #}
            }

            $item->upc = strval($x->UPC);

            $result->add($item);
        }

        $this->status = Response::STATUS_OK;

        return $result;
    }
}
