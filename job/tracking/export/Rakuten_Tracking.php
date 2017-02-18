<?php

use Shipment\RakutenShipmentFile;

class Rakuten_Tracking extends TrackingExporter
{
    public function export()
    {
        $orders = $this->getUnshippedOrders('Rakuten');
        $filename = Filenames::get('rakuten.us.shipping');
        $this->exportTracking('US', $orders, $filename);
    }

    protected function exportTracking($country, $orders, $filename)
    {
        $file = new RakutenShipmentFile($country, $filename);
       #$file->write($data);
    }

    protected function getUnshippedOrders($channel)
    {
    }
}
