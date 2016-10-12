<?php

namespace Supplier;

use Phalcon\Di;

class PurchaseOrderLog
{
    public static function saveXml($url, $request, $response)
    {
        $db = Di::getDefault()->get('db');

        $db->insertAsDict('xmlapi_po_log',
            [
                'sku' => $request->getSku(),
                'order_id' => $request->getOrderId(),
                'url' => $url,
                'request' => $request->toXml(),
                'response' => $response->getXmlDoc(),
               #'status' => $response->getStatus(),
            ]
        );
    }

    public static function save($sku, $orderId, $poNumber)
    {
        $db = Di::getDefault()->get('db');

        try {
            $db->insertAsDict('purchase_order_log',
                [
                    'sku' => $sku,
                    'orderid' => $orderId,
                    'ponumber' => $poNumber,
                ]
            );
        } catch (\Exception $e) {
            $logger = Di::getDefault()->get('logger');
            $logger->error($e->getMessage());
        }
    }
}
