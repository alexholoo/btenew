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

        // TODO: need a class: Marketplace\Newegg\MasterOrderFile
        $orderFile = 'w:/data/csv/newegg/canada_order/neweggcanada_master_orders.csv';

        if (!file_exists($orderFile)) {
            $this->error("File not found: $orderFile");
            return [];
        }

        $fp = fopen($orderFile, 'r');

        $columns = fgetcsv($fp);
        /*
         0   Order Number,
         1   Order Date & Time,
         2   Sales Channel,
         3   Fulfillment Option,
         4   Ship To Address Line 1,
         5   Ship To Address Line 2,
         6   Ship To City,
         7   Ship To State,
         8   Ship To ZipCode,
         9   Ship To Country,
         10  Ship To First Name,
         11  Ship To LastName,
         12  Ship To Company,
         13  Ship To Phone Number,
         14  Order Customer Email,
         15  Order Shipping Method,
         16  Item Seller Part #,
         17  Item Newegg #,
         18  Item Unit Price,
         19  Extend Unit Price,
         20  Item Unit Shipping Charge,
         21  Extend Shipping Charge,
         22  Order Shipping Total,
         23  Order Discount Amount,
         24  GST or HST Total,
         25  PST or QST Total,
         26  Order Total,
         27  Quantity Ordered,
         28  Quantity Shipped,
         29  ShipDate,
         30  Actual Shipping Carrier,
         31  Actual Shipping Method,
         32  Tracking Number,
         33  Ship From Address,
         34  Ship From City,
         35  Ship From State,
         36  Ship From Zipcode,
         37  Ship From Name
        */
        $orders = [];

        while (($values = fgetcsv($fp)) !== FALSE) {
            if (count($columns) != count($values)) {
                $this->error(__METHOD__. print_r($values, true));
                continue;
            }
            $fields = array_combine($columns, $values);

            $orderId = $fields['Order Number'];

            if ($shipmentService->isOrderShipped($orderId)) {
                continue;
            }

            $tracking = $shipmentService->getOrderTracking($orderId);

            if ($tracking) {
                $order = $fields;

                $order['Quantity Shipped']        = $fields['Quantity Ordered'];
                $order['ShipDate']                = $tracking['shipDate'];
                $order['Actual Shipping Carrier'] = $tracking['carrier'];
                $order['Actual Shipping Method']  = $fields['Order Shipping Method'];
                $order['Tracking Number']         = $tracking['trackingNumber'];

                $orders[]  = $order;
            }
        }

        fclose($fp);

        return $orders;
    }
}
