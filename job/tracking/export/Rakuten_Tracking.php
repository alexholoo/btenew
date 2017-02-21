<?php

use Shipment\RakutenShipmentFile;

class Rakuten_Tracking extends TrackingExporter
{
    public function export()
    {
        $orders = $this->getUnshippedOrders('Rakuten-BUY');
        $filename = Filenames::get('rakuten.us.shipping');
        $this->exportTracking('US', $orders, $filename);
    }

    protected function exportTracking($country, $orders, $filename)
    {
        $file = new RakutenShipmentFile($country, $filename);
        foreach ($orders as $order) {
            $file->write([ // TODO
                $order['orderId'],        //'receipt-id',
                '',                       //'receipt-item-id',
                '',                       //'quantity',
                $order['carrier'],        //'tracking-type',
                $order['trackingNumber'], //'tracking-number',
                $order['shipDate'],       //'ship-date',
            ]);
        }
    }

    protected function getUnshippedOrders($channel)
    {
        $sql = "SELECT t.order_id as orderId,
                       t.ship_date as shipDate,
                       t.carrier,
                       t.ship_method as shipMethod,
                       t.tracking_number as trackingNumber
                  FROM master_order_tracking t
             LEFT JOIN master_order o ON t.order_id=o.order_id
             LEFT JOIN master_order_shipped s ON t.order_id=s.order_id
                 WHERE o.channel='$channel' AND s.createdon IS NULL";

        $result = $this->db->fetchAll($sql);
        if (!$result) {
            return [];
        }
        return $result;
    }
}
