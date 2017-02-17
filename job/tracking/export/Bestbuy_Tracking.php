<?php

class Bestbuy_Tracking extends TrackingExporter
{
    public function export()
    {
        $orders = $this->getUnshippedOrders();
        $filename = 'w:/out/ship/bestbuy_shipping.csv';
        $this->exportTracking($orders, $filename);
    }

    protected function exportTracking($orders, $filename)
    {
    }

    protected function getUnshippedOrders()
    {
    }
}
