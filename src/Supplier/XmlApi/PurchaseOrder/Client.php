<?php

namespace Supplier\XmlApi\PurchaseOrder;

use Supplier\XmlApi\Client as XmlApiClient;

abstract class Client extends XmlApiClient
{
    protected function saveLog($url, $request, $response)
    {
        $this->db->insertAsDict('xmlapi_po_log',
            [
                'sku' => $request->getSku(),
                'url' => $url,
                'request' => $request->toXml(),
                'response' => $response->getXmlDoc(),
                'status' => $response->getStatus(),
            ]
        );
    }
}
