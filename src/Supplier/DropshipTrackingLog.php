<?php

namespace Supplier;

use Phalcon\Di;

class DropshipTrackingLog
{
    /**
     * Save the order tracking for future use
     * @param  Supplier\Model\OrderStatusResult $result
     */
    public static function save($result)
    {
        $db = Di::getDefault()->get('db');

        try {
            $db->insertAsDict('dropship_tracking', [
                'orderid'     => $result->orderNo,
                'shipdate'    => $result->shipDate,
                'carrier'     => $result->carrier,
                'service'     => $result->service,
                'trackingnum' => $result->trackingNumber,
            ]);
        } catch (\Exception $e) {
            $logger = Di::getDefault()->get('logger');
            $logger->error($e->getMessage());
        }
    }

    /**
     * Mark the order tracking as 'uploaded'
     * @param  string $orderId
     */
    public static function update($orderId)
    {
        $db = Di::getDefault()->get('db');

        try {
            $sql = "UPDATE dropship_tracking SET uploaded = 1 WHERE orderid = '$orderId'";
            $db->execute($sql);
        } catch (\Exception $e) {
            $logger = Di::getDefault()->get('logger');
            $logger->error($e->getMessage());
        }
    }
}
