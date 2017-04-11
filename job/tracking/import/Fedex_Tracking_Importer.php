<?php

class Fedex_Tracking_Importer extends Tracking_Importer
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
        $filename = Filenames::get('fedex.tracking');

        if (!file_exists($filename)) {
            $this->error(__METHOD__." File not found: $filename");
            return;
        }

        $fp = fopen($filename, 'r');

        while (($line1 = fgets($fp)) !== FALSE) {
            if (substr(trim($line1), -6, 4) == 'Page') {
                continue;
            }

            $line2 = fgets($fp);

            $line = trim($line1) . trim($line2);

            if (!preg_match('%\d{2}/\d{2}/\d{4} \d{2}:\d{2} \d{12}%', $line)) {
                continue;
            }

            $shipDate = substr($line, 0, 10);
            list($m, $d, $y) = explode('/', $shipDate);
            $shipDate = "$y-$m-$d";

            $trackingNumber = substr($line, 17, 12);
            $orderId = substr($line, strrpos($line, ' ') + 1);

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
