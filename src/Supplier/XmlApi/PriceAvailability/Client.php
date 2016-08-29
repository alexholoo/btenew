<?php

namespace Supplier\XmlApi\PriceAvailability;

use Supplier\XmlApi\Client as XmlApiClient;

abstract class Client extends XmlApiClient
{
    protected function saveLog($url, $request, $response)
    {
        $this->db->insertAsDict('xmlapi_pna_log',
            [
                'sku' => $request->getPartnum(),
                'url' => $url,
                'request' => $request->toXml(),
                'response' => $response->getXmlDoc(),
                'status' => $response->getStatus(),
            ]
        );
    }
}
