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
       #$file->write($data);
    }

    protected function getUnshippedOrders($channel)
    {
    }
}
