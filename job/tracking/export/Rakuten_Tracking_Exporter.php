<?php

use Shipment\RakutenShipmentFile;

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
        $file = new RakutenShipmentFile($country, $filename);
        foreach ($orders as $order) {
            $file->write($order);
        }
    }

    protected function getUnshippedOrders($channel)
    {
        $shipmentService = $this->di->get('shipmentService');

        // TODO: need a class: Marketplace\Rakuten\MasterOrderFile
        $orderFile = 'w:/data/csv/rakuten/orders/rakuten_master_orders.csv';

        if (!file_exists($orderFile)) {
            $this->error(__METHOD__." File not found: $orderFile");
            return [];
        }

        $fp = fopen($orderFile, 'r');

        $columns = fgetcsv($fp);
        /*
         0   SellerShopperNumber,
         1   Receipt_ID,
         2   Receipt_Item_ID,
         3   ListingID,
         4   Date_Entered,
         5   Sku,
         6   ReferenceId,
         7   Quantity,
         8   Qty_Shipped,
         9   Qty_Cancelled,
         10  Title,
         11  Price,
         12  Product_Rev,
         13  Shipping_Cost,
         14  ProductOwed,
         15  ShippingOwed,
         16  Commission,
         17  ShippingFee,
         18  PerItemFee,
         19  Tax_Cost,
         20  Bill_To_Company,
         21  Bill_To_Phone,
         22  Bill_To_Fname,
         23  Bill_To_Lname,
         24  Email,
         25  Ship_To_Name,
         26  Ship_To_Company,
         27  Ship_To_Street1,
         28  Ship_To_Street2,
         29  Ship_To_City,
         30  Ship_To_State,
         31  Ship_To_Zip,
         32  ShippingMethodId
        */

        $orders = [];

        while (($values = fgetcsv($fp)) !== FALSE) {
            if (count($columns) != count($values)) {
                $this->error(__METHOD__. print_r($values, true));
                continue;
            }
            $fields = array_combine($columns, $values);

            $receiptId     = $fields['Receipt_ID']; // rakuten order id
            $receiptItemId = $fields['Receipt_Item_ID'];
            $qty           = $fields['Quantity'];

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

        fclose($fp);

        return $orders;
    }
}
