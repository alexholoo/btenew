<?php

use Marketplace\Rakuten\ShipmentFile;

class Rakuten_Tracking_Exporter extends Tracking_Exporter
{
    public function run($argv = [])
    {
        try {
            $this->export();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function export()
    {
        $orders = $this->getUnshippedOrders('Rakuten-BUY');
        $filename = Filenames::get('rakuten.us.shipping');
        $this->exportTracking('US', $orders, $filename);
    }

    protected function exportTracking($country, $orders, $filename)
    {
        $file = new ShipmentFile($country, $filename);
        foreach ($orders as $order) {
            $file->write($order);
        }
    }

    // TODO: get unshipped orders from database, instead of file
    protected function getUnshippedOrders($channel)
    {
        $shipmentService = $this->di->get('shipmentService');

        $filename = Filenames::get('rakuten.us.master.order');
        $orderFile = new Marketplace\Rakuten\StdOrderListFile($filename, 'US');

        $orders = [];

        while ($order = $orderFile->read()) {
            $receiptId     = $order['Receipt_ID']; // rakuten order id
            $receiptItemId = $order['Receipt_Item_ID'];
            $qty           = $order['Quantity'];

            if ($shipmentService->isOrderShipped($receiptId)) {
                continue;
            }

            $tracking = $shipmentService->getOrderTracking($receiptId);

            if ($tracking) {
                $trackingType = '5'; // other courier

                if (strtoupper($tracking['carrierCode']) == 'USPS') {
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

        return $orders;
    }
}
