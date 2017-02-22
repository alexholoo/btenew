<?php

use Shipment\RakutenShipmentFile;

class Rakuten_Tracking extends TrackingExporter
{
    public function export()
    {
        $orders = $this->getUnshippedOrders('Rakuten-BUY');
        $filename = Filenames::get('rakuten.us.shipping');
        $this->exportTracking('US', $orders, $filename);
    }

    protected function exportTracking($country, $orders, $filename)
    {
        $file = new RakutenShipmentFile($country, $filename);
        foreach ($orders as $order) {
            $file->write($order);
        }
    }

    protected function getUnshippedOrders($channel)
    {
        $shipmentService = $this->di->get('shipmentService');

        $orderFile = 'w:/data/csv/rakuten/orders/rakuten_master_orders.csv';

        if (!($fp = fopen($orderFile, 'r'))) {
            $this->error("File not found: $orderFile");
            return [];
        }

        $orders = [];

        while (($fields = fgetcsv($fp)) !== FALSE) {

            // TODO: fix the hard code
            $receiptId     = $fields[1]; // rakuten order id
            $receiptItemId = $fields[2];
            $qty           = $fields[7];

            if ($shipmentService->isOrderShipped($receiptId)) {
                continue;
            }

            $tracking = $shipmentService->getOrderTracking($orderId);

            if ($tracking) {
                $trackingType = '5'; // other courier

                if (strtoupper($tracking['carrier']) == 'USPS') {
                    $trackingType = '3';
                }

                // TODO: more carrier codes

                $shipDate = date('m/d/Y', strtotime($tracking['shipDate']));

                $orders[] = [
                    $receiptId,                   // 'receipt-id'
                    $receiptItemId,               // 'receipt-item-id'
                    $qty,                         // 'quantity'
                    $trackingType,                // 'tracking-type'
                    $tracking['trackingNumber'],  // 'tracking-number'
                    $shipDate,                    // 'ship-date'
                ];
            }
        }

        fclose($fp);

        return $orders;
    }
}
