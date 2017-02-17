<?php

use Shipment\RakutenShipmentFile;

class Rakuten_Tracking extends TrackingExporter
{
    public function export()
    {
        $orders = $this->getUnshippedOrders('Rakuten');
        $filename = 'w:/out/shipping/rakuten_tracking.txt';
        $this->exportTracking('US', $orders, $filename);
    }

    protected function exportTracking($country, $orders, $filename)
    {
        $file = new RakutenShipmentFile($country);
       #$file->write($data);
    }

    protected function getUnshippedOrders($channel)
    {
    }
}
