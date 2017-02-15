<?php

class ShippingEasy_Tracking extends TrackingCollector
{
    public function collect()
    {
        $filename = 'w:/out/shipping/shippingeasy-shipping-report.csv';

        if (($fp = fopen($filename, 'r')) == false) {
            $this->error("File not found: $filename");
            return;
        }

        // skip the first few lines
        fgetcsv($fp); fgetcsv($fp); fgetcsv($fp);
        fgetcsv($fp); fgetcsv($fp); fgetcsv($fp);

        while ($fields = fgetcsv($fp)) {

            $orderId        = trim($fields[5]);
            $shipDate       = $fields[0];
            $carrier        = $fields[12];
            $trackingNumber = ltrim($fields[23], "'");

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
