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
        foreach ($orders as $order) {
            $file->write($order);
        }
    }

    protected function getUnshippedOrders($channel)
    {
        $shipmentService = $this->di->get('shipmentService');

        $orderFile = 'w:/data/csv/newegg/canada_order/neweggcanada_master_orders.csv';

        if (!($fp = fopen($orderFile, 'r'))) {
            $this->error("File not found: $orderFile");
            return [];
        }

        $orders = [];

        while (($fields = fgetcsv($fp)) !== FALSE) {

            $order   = $fields;
            $orderId = $fields[0];

            if ($shipmentService->isOrderShipped($orderId)) {
                continue;
            }

            $tracking = $shipmentService->getOrderTracking($orderId);

            if ($tracking) {
                // TODO: fix
                $order[27] = $fields[26];                 // qty shipped is qty ordered
                $order[28] = $tracking['shipDate'];       // shipment date
                $order[29] = $tracking['carrier'];        // shipment carrier
                $order[30] = $fields[15];                 // shipping method as per order specified
                $order[31] = $tracking['trackingNumber']; // shipment tracking

                $orders[]  = $order;
            }
        }

        fclose($fp);

        return $orders;
    }
}
