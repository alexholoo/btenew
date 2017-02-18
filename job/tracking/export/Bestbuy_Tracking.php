<?php

class Bestbuy_Tracking extends TrackingExporter
{
    public function export()
    {
        $orders = $this->getUnshippedOrders();
        $filename = Filenames::get('bestbuy.shipping');
        $this->exportTracking($orders, $filename);
    }

    protected function exportTracking($orders, $filename)
    {
    }

    protected function getUnshippedOrders()
    {
    }
}
