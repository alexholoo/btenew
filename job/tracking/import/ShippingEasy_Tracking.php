<?php

class ShippingEasy_Tracking extends TrackingImporter
{
    public function import()
    {
        $filename = Filenames::get('shippingeasy.tracking');

        if (($fp = fopen($filename, 'r')) == false) {
            $this->error("File not found: $filename");
            return;
        }

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

        while ($fields = fgetcsv($fp)) {
            if (count($columns) != count($fields)) {
                $this->error(__METHOD__ . print_r($fields, true));
                continue;
            }

            $data = array_combine($columns, $fields);

            $orderId        = trim($data['Order Number']);
            $shipDate       = $data['Ship Date'];
            $carrier        = $data['Carrier'];
            $trackingNumber = ltrim($data['Tracking Number'], "'");

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
