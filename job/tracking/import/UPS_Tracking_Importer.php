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
        $trackings = $this->getTrackings();

        foreach ($trackings as $tracking) {
            $this->saveToDb([
                'orderId'        => $tracking['orderId'],
                'shipDate'       => $tracking['shipDate'],
                'carrierCode'    => 'UPS',
                'carrierName'    => '',
                'shipMethod'     => '',
                'trackingNumber' => $tracking['trackingNumber'],
                'sender'         => 'BTE',
            ]);
        }
    }

    protected function getTrackings()
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

        $trackings = [];

        while (($fields = fgetcsv($fp)) !== FALSE) {

            $orderId = $fields[1];
            if (empty($orderId)) {
                continue;
            }

           #$shipDate = $fields[3];

           #if (strlen($shipDate) < 10) { // not end-of-day yet
           #    continue;
           #}

            $shipDate  = date('Y-m-d', strtotime($fields[3]));
            $trackingNumber = $fields[2];

            $trackings[$orderId] = [
                'orderId'        => $orderId,
                'shipDate'       => $shipDate,
                'trackingNumber' => $trackingNumber,
            ];
        }

        fclose($fp);

        return $trackings;
    }
}
