<?php

use Shipment\EbayShipmentFile;

class Ebay_Tracking extends TrackingExporter
{
    public function export()
    {
        // BTE
        $filename = 'w:/out/ship/ebay_shipping_bte.csv';
        $filename = 'E:/BTE/ship/ebay_shipping_bte.csv';
        $orders = $this->getUnshippedOrders('eBay-bte');
        $this->exportTracking($orders, $filename);

        // ODO
        $orders = $this->getUnshippedOrders('eBay-odo');
        $filename = 'w:/out/ship/ebay_shipping_odo.csv';
        $filename = 'E:/BTE/ship/ebay_shipping_odo.csv';
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
