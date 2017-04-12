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

        $lines = file($filename, FILE_IGNORE_NEW_LINES);

       #$pattern = '/Man-Wt/'; // this also works!
        $pattern = '%\d{2}/\d{2}/\d{4} \d{2}:\d{2} \d{12}%';

        $i = 0;
        while ($i < count($lines)) {
            $line = trim($lines[$i++]);
            if (!preg_match($pattern, $line)) {
                continue;
            }
            if (isset($lines[$i]) && !preg_match($pattern, $lines[$i])) {
                $line .= trim($lines[$i++]);
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
    }
}
