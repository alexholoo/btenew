<?php

class Rakuten_Tracking extends TrackingExporter
{
    public function export()
    {
        $orders = $this->getUnshippedOrders('Rakuten');
        $filename = 'w:/out/shipping/rakuten_tracking.txt';
        $this->exportTracking($orders, $filename);
    }

    protected function exportTracking($orders, $filename)
    {
        $columns = [ 'receipt-id', 'receipt-item-id', 'quantity', 'tracking-type', 'tracking-number', 'ship-date' ];
    }

    protected function getUnshippedOrders($channel)
    {
    }
}
