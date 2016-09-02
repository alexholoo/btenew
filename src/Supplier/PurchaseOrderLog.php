<?php

namespace Supplier;

use Phalcon\Di;

class PurchaseOrderLog
{
    public static function save($url, $request, $response)
    {
        $db = Di::getDefault()->get('db');

        $db->insertAsDict('xmlapi_po_log',
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
