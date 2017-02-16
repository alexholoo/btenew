<?php

class UPS_Tracking extends TrackingCollector
{
    public function collect()
    {
        $filename = 'w:/out/shipping/UPS/UPS_CSV_EXPORT.csv';

        if (($fp = fopen($filename, 'r')) == false) {
            $this->error("File not found: $filename");
            return;
        }

        while (($fields = fgetcsv($fp)) !== FALSE) {

            $orderId = $fields[1];
            if (empty($orderId)) {
                continue;
            }

            $shipDate = $fields[3];
            if (strlen($shipDate) < 10) {
                continue;
            }

            $shipDate  = date('Y-m-d', strtotime($fields[3]));
            $trackingNumber = $fields[2];

            $this->saveToDb([
                'orderId'        => $orderId,
                'shipDate'       => $shipDate,
                'carrier'        => 'UPS',
                'shipMethod'     => '',
                'trackingNumber' => $trackingNumber,
                'sender'         => 'BTE',
            ]);
        }

        fclose($fp);
    }
}
