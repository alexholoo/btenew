<?php

class TNT_Tracking extends TrackingImporter
{
    public function import()
    {
        $filename = 'w:/out/shipping/tntshipments.csv';

        if (($fp = fopen($filename, 'r')) == false) {
            $this->error("File not found: $filename");
            return;
        }

        while (($fields = fgetcsv($fp)) !== FALSE) {

            $shipmentStatus = $fields[11];

            if ($shipmentStatus == 'Shipped') {

                $orderId        = $fields[1];
                $shipDate       = date('Y-m-d', strtotime($fields[6]));
                $carrierCode    = 'TNT';
                $trackingNumber = $fields[0];
                $shipMethod     = 'Standard';

                $this->saveToDb([
                    'orderId'        => $orderId,
                    'shipDate'       => $shipDate,
                    'carrier'        => $carrierCode,
                    'shipMethod'     => $shipMethod,
                    'trackingNumber' => $trackingNumber,
                    'sender'         => 'TNT',
                ]);
            }
        }

        fclose($fp);
    }
}
