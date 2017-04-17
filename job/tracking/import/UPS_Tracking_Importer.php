<?php

class UPS_Tracking_Importer extends Tracking_Importer
{
    public function run($argv = [])
    {
        try {
            $this->import();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function import()
    {
        $filename = Filenames::get('ups.tracking');

        if (!file_exists($filename)) {
            $this->error(__METHOD__." File not found: $filename");
            return;
        }

        $fp = fopen($filename, 'r');

        /**
         * ,"114-6686534-2495411","1Z37Y0596840271065","20170221161655"
         *
         * 0 - empty
         * 1 - orderId
         * 2 - tracking number
         * 3 - shipping date
         */

        $today = date('Y-m-d');
        $trackings = [];

        while (($fields = fgetcsv($fp)) !== FALSE) {

            $orderId = $fields[1];
            if (empty($orderId)) {
                continue;
            }

            $shipDate = $fields[3];

           #if (strlen($shipDate) < 10) { // not end-of-day yet
           #    continue;
           #}

            $shipDate  = date('Y-m-d', strtotime($fields[3]));
            $trackingNumber = $fields[2];

            $this->saveToDb([
                'orderId'        => $orderId,
                'shipDate'       => $shipDate,
                'carrierCode'    => 'UPS',
                'carrierName'    => '',
                'shipMethod'     => '',
                'trackingNumber' => $trackingNumber,
                'sender'         => 'BTE',
            ]);

            if ($shipDate == $today) {
                if (isset($trackings[$orderId])) {
                    $trackings[$orderId]++;
                } else {
                    $trackings[$orderId] = 1;
                }
            }
        }

        $trackings = array_filter($trackings, function($a) { return $a > 2; });
        if ($trackings) {
            $this->error(__METHOD__. " Multiple Tracking Numbers:\n". print_r($trackings, true));
        }

        fclose($fp);
    }
}
