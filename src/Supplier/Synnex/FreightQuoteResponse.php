<?php

namespace Supplier\Synnex;

use Supplier\Model\Response as BaseResponse;

class FreightQuoteResponse extends BaseResponse
{
    /**
     * @return Supplier\Model\FreightQuoteResult
     */
    public function parseXml()
    {
        $xml = simplexml_load_string($this->xmldoc);

        $result = new FreightQuoteResult();

        if ($xml->FreightQuoteResponse->ErrorMessage) {
            $result->errorMessage = strval($xml->FreightQuoteResponse->ErrorMessage);
            $result->errorDetail  = strval($xml->FreightQuoteResponse->ErrorDetail);
            return $result;
        }

        $shipMethods = $xml->FreightQuoteResponse->AvailableShipMethods->AvailableShipMethod;

        foreach ($shipMethods as $shipMethod) {
            $result->add([
                'Code'         => strval($shipMethod['code']),
                'Description'  => strval($shipMethod->ShipMethodDescription),
                'ServiceLevel' => strval($shipMethod->ServiceLevel),
                'Freight'      => strval($shipMethod->Freight),
            ]);
        }

        return $result;
    }
}
