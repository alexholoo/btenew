<?php

use Shipment\NeweggShipmentFile;

class Newegg_Tracking extends TrackingExporter
{
    public function export()
    {
        $orders = $this->getUnshippedOrders('NeweggCA');
        $filename = 'w:/out/shipping/newegg_canada_tracking.csv';
        $this->exportTracking('CA', $orders, $filename);
    }

    protected function exportTracking($country, $orders, $filename)
    {
        $file = new NeweggShipmentFile($country);
       #$file->write($data);
    }

    protected function getUnshippedOrders($channel)
    {
    }
}
