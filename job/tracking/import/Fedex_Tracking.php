<?php

class Fedex_Tracking extends TrackingImporter
{
    public function import()
    {
        $filename = Filenames::get('fedex.tracking');

        if (!file_exists($filename)) {
            $this->error(__METHOD__." File not found: $filename");
            return;
        }

        $fp = fopen($filename, 'r');

        // skip first 5 lines
        fgets($fp);
        fgets($fp);
        fgets($fp);
        fgets($fp);
        fgets($fp);

        while (($line1 = fgets($fp)) !== FALSE) {
            if (substr($line1, 135, 4) == 'Page') {
                continue;
            }

            $line2 = fgets($fp);

            $line = trim($line1) . trim($line2);

            if (strlen($line) == 0) {
                continue;
            }

            if (substr($line, 112, 4) == 'CAFE') {
                continue;
            }

            if (substr($line, -5) == '*****') {
                continue;
            }

            $shipDate = substr($line, 0, 10);
            list($m, $d, $y) = explode('/', $shipDate);
            $shipDate = "$y-$m-$d";

            $trackingNumber = substr($line, 17, 12);
            $orderId = substr($line, 114);

            $this->saveToDb([
                'orderId'        => $orderId,
                'shipDate'       => $shipDate,
                'carrierCode'    => 'Fedex',
                'carrierName'    => '',
                'shipMethod'     => '',
                'trackingNumber' => $trackingNumber,
                'sender'         => 'BTE',
            ]);
        }

        fclose($fp);
    }
}
