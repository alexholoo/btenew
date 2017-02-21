<?php

use Shipment\NeweggShipmentFile;

class Newegg_Tracking extends TrackingExporter
{
    public function export()
    {
        $orders = $this->getUnshippedOrders('NeweggCA');
        $filename = Filenames::get('newegg.ca.shipping');
        $this->exportTracking('CA', $orders, $filename);
    }

    protected function exportTracking($country, $orders, $filename)
    {
        $file = new NeweggShipmentFile($country, $filename);
        foreach ($orders as $order) {
            $file->write($order); // TODO: not correct
        }
    }

    protected function getUnshippedOrders($channel)
    {
        $sql = "SELECT t.order_id AS orderId,
                       t.ship_date AS shipDate,
                       t.carrier,
                       t.ship_method AS shipMethod,
                       t.tracking_number AS trackingNumber
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
