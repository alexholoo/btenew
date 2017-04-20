<?php

use Marketplace\eBay\ShipmentFile;

class Ebay_Tracking_Exporter extends Tracking_Exporter
{
    public function run($argv = [])
    {
        try {
            $this->export();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function export()
    {
        // GFS
        $orders = $this->getUnshippedOrders('eBay-GFS');
        $filename = Filenames::get('ebay.gfs.shipping');
        $this->exportTracking($orders, $filename);

        // ODO
        $orders = $this->getUnshippedOrders('eBay-ODO');
        $filename = Filenames::get('ebay.odo.shipping');
        $this->exportTracking($orders, $filename);
    }

    protected function exportTracking($orders, $filename)
    {
        $file = new ShipmentFile($filename);

        foreach ($orders as $order) {
            $order['transactionID'] = '';
            $file->write($order);
        }
    }

    protected function getUnshippedOrders($channel)
    {
        // the columns must match the columns in ShipmentFile
        $sql = "SELECT o.order_id        AS orderID,
                       o.reference       AS recordNumber,
                       t.ship_date       AS shipDate,
                       t.carrier_code    AS carrier,
                       t.tracking_number AS trackingNumber

                  FROM master_order_tracking t
             LEFT JOIN master_order          o ON t.order_id=o.reference
             LEFT JOIN master_order_shipped  s ON t.order_id=s.order_id

                 WHERE o.channel='$channel' AND s.createdon IS NULL";

        $result = $this->db->fetchAll($sql);
        if (!$result) {
            return [];
        }
        return $result;
    }
}
