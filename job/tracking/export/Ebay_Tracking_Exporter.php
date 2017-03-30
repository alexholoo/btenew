<?php

use Shipment\EbayShipmentFile;

class Ebay_Tracking_Exporter extends Tracking_Exporter
{
    public function export()
    {
        // BTE
        $orders = $this->getUnshippedOrders('eBay'); // channel?
        $filename = Filenames::get('ebay.gfs.shipping');
        $this->exportTracking($orders, $filename);

        // ODO
        $orders = $this->getUnshippedOrders('eBay'); // channel?
        $filename = Filenames::get('ebay.odo.shipping');
        $this->exportTracking($orders, $filename);
    }

    protected function exportTracking($orders, $filename)
    {
        $file = new EbayShipmentFile($filename);

        foreach ($orders as $order) {
            $file->write([
                $order['orderId'],
                $order['shipDate'],
                $order['carrierCode'],
                $order['trackingNumber'],
                '', // 'TransactionID'
            ]);
        }
    }

    protected function getUnshippedOrders($channel)
    {
        $sql = "SELECT t.order_id        AS orderId,
                       t.ship_date       AS shipDate,
                       t.carrier_code    AS carrierCode,
                       t.carrier_name    AS carrierName,
                       t.ship_method     AS shipMethod,
                       t.tracking_number AS trackingNumber

                  FROM master_order_tracking t
             LEFT JOIN master_order          o ON t.order_id=o.order_id
             LEFT JOIN master_order_shipped  s ON t.order_id=s.order_id

                 WHERE o.channel='$channel' AND s.createdon IS NULL";

        $result = $this->db->fetchAll($sql);
        if (!$result) {
            return [];
        }
        return $result;
    }
}
