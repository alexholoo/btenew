<?php

use Shipment\EbayShipmentFile;

class Ebay_Tracking extends TrackingExporter
{
    public function export()
    {
        // BTE
        $filename = Filenames::get('ebay.bte.shipping');
        $orders = $this->getUnshippedOrders('eBay-bte');
        $this->exportTracking($orders, $filename);

        // ODO
        $orders = $this->getUnshippedOrders('eBay-odo');
        $filename = Filenames::get('ebay.odo.shipping');
        $this->exportTracking($orders, $filename);
    }

    protected function exportTracking($orders, $filename)
    {
        $file = new EbayShipmentFile($filename);
       #$file->write($data);
    }

    protected function getUnshippedOrders($channel)
    {
    }
}
