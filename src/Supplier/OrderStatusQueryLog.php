<?php

namespace Supplier;

use Phalcon\Di;

class OrderStatusQueryLog
{
    public static function save($orderId, $url, $request, $response)
    {
        $db = Di::getDefault()->get('db');

        $db->insertAsDict('xmlapi_os_log', [
            'url'      => $url,
            'order_id' => $orderId,
            'request'  => $request,
            'response' => $response,
        ]);
    }
}
