<?php

class EShipper_Tracking extends TrackingImporter
{
    public function import()
    {
        $filename = 'w:/out/shipping/eshipper_shipment.csv';

        if (($fp = fopen($filename, 'r')) == false) {
            $this->error("File not found: $filename");
            return;
        }

        $title = fgetcsv($fp);

        while (($fields = fgetcsv($fp))!== FALSE) {

            $orderId        = $fields[2];
            $trackingNumber = $fields[22];
            $shipDate       = $fields[0];
            $carrier        = $fields[3];
            $shipMethod     = $fields[4];

            $this->saveToDb([
                'orderId'        => $orderId,
                'shipDate'       => $shipDate,
                'carrier'        => $carrier,
                'shipMethod'     => $shipMethod,
                'trackingNumber' => $trackingNumber,
                'sender'         => 'eShipper',
            ]);
        }

        fclose ($fp);
    }
}
