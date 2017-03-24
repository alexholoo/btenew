<?php

use Shipment\NeweggShipmentFile;
use Marketplace\Newegg\StdOrderListFile;

class Newegg_Tracking_Exporter extends Tracking_Exporter
{
    public function export()
    {
        // CA
        $orderFile = Filenames::get('newegg.ca.master.order');
        if (file_exists($orderFile)) {
            $orders = $this->getUnshippedOrders('CA', $orderFile);
            $filename = Filenames::get('newegg.ca.shipping');
            $this->exportTracking('CA', $orders, $filename);
        }

        // US
        $orderFile = Filenames::get('newegg.us.master.order');
        if (file_exists($orderFile)) {
            $orders = $this->getUnshippedOrders('US', $orderFile);
            $filename = Filenames::get('newegg.us.shipping');
            $this->exportTracking('US', $orders, $filename);
        }
    }

    protected function exportTracking($site, $orders, $filename)
    {
        $file = new NeweggShipmentFile($filename, $site);
        foreach ($orders as $order) {
            $file->write($order);
        }
    }

    protected function getUnshippedOrders($site, $filename)
    {
        $shipmentService = $this->di->get('shipmentService');

        $orderFile = new StdOrderListFile($filename, $site);

        $orders = [];

        while (($fields = $orderFile->read())) {

            $orderId = $fields['Order Number'];

            if ($shipmentService->isOrderShipped($orderId)) {
                continue;
            }

            $tracking = $shipmentService->getOrderTracking($orderId);

            if ($tracking) {
                if ($tracking['carrierCode'] == 'Other') {
                    $tracking['carrierCode'] = $tracking['carrierName'];
                }

                $order = $fields;

                $order['Quantity Shipped']        = $fields['Quantity Ordered'];
                $order['ShipDate']                = $tracking['shipDate'];
                $order['Actual Shipping Carrier'] = $tracking['carrierCode'];
                $order['Actual Shipping Method']  = $fields['Order Shipping Method'];
                $order['Tracking Number']         = $tracking['trackingNumber'];

                $orders[] = $order;
            }
        }

        return $orders;
    }
}
