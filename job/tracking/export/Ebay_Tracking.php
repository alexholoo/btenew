<?php

use Shipment\EbayShipmentFile;

class Ebay_Tracking extends TrackingExporter
{
    public function export()
    {
        $filename = 'w:/out/ship/ebay_shipping_bte.csv';
        $orders = $this->getUnshippedOrders('eBay-bte');
        $this->exportTracking($orders, $filename);

        $orders = $this->getUnshippedOrders('eBay-odo');
        $filename = 'w:/out/ship/ebay_shipping_odo.csv';
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
