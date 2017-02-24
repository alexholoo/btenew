<?php

class ShippingEasy_Tracking extends TrackingImporter
{
    public function import()
    {
        $filename = Filenames::get('shippingeasy.tracking');

        if (!file_exists($filename)) {
            $this->error("File not found: $filename");
            return;
        }

        $fp = fopen($filename, 'r');

        // TODO: need a class: Shipment\ShippingEasyTrackingFile

        // skip the first few lines
        fgetcsv($fp); fgetcsv($fp); fgetcsv($fp);
        fgetcsv($fp); fgetcsv($fp);

        $columns = fgetcsv($fp);
        /*
            Ship Date,
            User,
            Order Date,
            Order Total,
            Store,
            Order Number,
            Ship From,
            Ship From Address,
            Recipient,
            Recipient Billing Address,
            Recipient Shipping Address,
            Email Address,
            Carrier,
            Rate Provider,
            Service Type,
            Package Type,
            Confirmation Option,
            Quantity,
            Weight (oz),
            Zone,
            Destination Country,
            Destination City,
            Destination State/Province,
            Tracking Number,
            Shipping Paid (by customer),
            Postage Cost,
            Insurance Cost,
            Total Shipping Cost,
            Shipping Margin,
            SKU,
            Item Name
        */

        while ($values = fgetcsv($fp)) {
            if (count($columns) != count($values)) {
                $this->error(__METHOD__ . print_r($values, true));
                continue;
            }

            $fields = array_combine($columns, $values);

            $orderId        = trim($fields['Order Number']);
            $shipDate       = $fields['Ship Date'];
            $carrier        = $fields['Carrier'];
            $trackingNumber = ltrim($fields['Tracking Number'], "'");

            $this->saveToDb([
                'orderId'        => $orderId,
                'shipDate'       => $shipDate,
                'carrier'        => $carrier,
                'shipMethod'     => '',
                'trackingNumber' => $trackingNumber,
                'sender'         => 'ShippingEasy',
            ]);
        }

        fclose($fp);
    }
}
