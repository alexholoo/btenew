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

    public static function save($order, $poNumber)
    {
        $db = Di::getDefault()->get('db');

        $flag = 'dropship';

        if (count($order->items) > 1) {
            $flag = $order->orderId;
        }

        foreach ($order->items as $item) {
            $orderId = count($order->items) > 1 ? $item->orderId : $order->orderId;
            try {
                $db->insertAsDict('purchase_order_log',
                    [
                        'sku'      => $item->sku,
                        'orderid'  => $orderId,
                        'ponumber' => $poNumber,
                        'flag'     => $flag,
                    ]
                );
            } catch (\Exception $e) {
                $logger = Di::getDefault()->get('logger');
                $logger->error($e->getMessage());
            }
        }
    }

    public static function markProcessed($orderId)
    {
        $db = Di::getDefault()->get('db');

        $flag = 'Manual Entry';

        try {
            $db->insertAsDict('purchase_order_log',
                [
                    'sku'      => $flag,
                    'orderid'  => $orderId,
                    'ponumber' => '',
                    'flag'     => $flag,
                ]
            );
        } catch (\Exception $e) {
            $logger = Di::getDefault()->get('logger');
            $logger->error($e->getMessage());
        }
    }

    /**
     * Mark the dropship order as 'shipped'
     */
    public static function markShipped($orderId)
    {
        $db = Di::getDefault()->get('db');

        try {
            $sql = "UPDATE purchase_order_log SET shipped = 1 WHERE orderid = '$orderId'";
            $db->execute($sql);
        } catch (\Exception $e) {
            $logger = Di::getDefault()->get('logger');
            $logger->error($e->getMessage());
        }
    }
}
