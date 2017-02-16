<?php

class Solu_Tracking extends TrackingImporter
{
    public function import()
    {
        $filename = 'w:/out/shipping/solu_shipment.csv';

        if (($fp = fopen($filename, 'r')) == false) {
            $this->error("File not found: $filename");
            return;
        }

        $title = fgetcsv($fp);

        while (($fields = fgetcsv($fp)) !== FALSE) {

            $orderId = str_replace(' ', '', $fields[13]);

            $trackingNumber = str_replace(' ', '', $fields[1]);
            $shipDate       = date('Y-m-d', strtotime(str_replace('/', '-', $fields[2])));
            $carrierCode    = $fields[3]; // i.e. DHL
            $shipMethod     = $fields[4]; // i.e. Express
            $fullAddress    = $fields[10];

            $this->saveToDb([
                'orderId'        => $orderId,
                'shipDate'       => $shipDate,
                'carrier'        => $carrierCode,
                'shipMethod'     => $shipMethod,
                'trackingNumber' => $trackingNumber,
                'sender'         => 'Solu',
            ]);
        }

        fclose ($fp);
    }
}
