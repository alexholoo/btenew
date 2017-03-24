<?php

use Shipment\BestbuyShipmentFile;

class Bestbuy_Tracking_Exporter extends Tracking_Exporter
{
    public function export()
    {
        $orders = $this->getUnshippedOrders();
        $filename = Filenames::get('bestbuy.shipping');
        $this->exportTracking($orders, $filename);
    }

    protected function exportTracking($orders, $filename)
    {
        $file = new BestbuyShipmentFile($filename);

        foreach ($orders as $order) {
            $file->write($order);
        }
    }

    protected function getUnshippedOrders()
    {
        $sql = "SELECT t.order_id            AS orderId,
                       t.ship_date           AS shipDate,
                       t.carrier_code        AS carrierCode,
                       t.carrier_name        AS carrierName,
                       t.ship_method         AS shipMethod,
                       t.tracking_number     AS trackingNumber

                  FROM master_order_tracking t
             LEFT JOIN master_order          o ON t.order_id=o.order_id
             LEFT JOIN master_order_shipped  s ON t.order_id=s.order_id

                 WHERE o.channel='Bestbuy' AND s.createdon IS NULL";

        $result = $this->db->fetchAll($sql);
        if (!$result) {
            return [];
        }
        return $result;
    }
}
