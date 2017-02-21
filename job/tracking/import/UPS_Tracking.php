<?php

class UPS_Tracking extends TrackingImporter
{
    public function import()
    {
        $filename = Filenames::get('ups.tracking');

        if (($fp = fopen($filename, 'r')) == false) {
            $this->error("File not found: $filename");
            return;
        }

        /**
         * ,"114-6686534-2495411","1Z37Y0596840271065","20170221161655"
         *
         * 0 - empty
         * 1 - orderId
         * 2 - tracking number
         * 3 - shipping date
         */

        while (($fields = fgetcsv($fp)) !== FALSE) {

            $orderId = $fields[1];
            if (empty($orderId)) {
                continue;
            }

            $shipDate = $fields[3];
            if (strlen($shipDate) < 10) { // not end-of-day yet
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
