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

        $columns = fgetcsv($fp);

        while (($values = fgetcsv($fp)) !== false) {
            if (count($columns) != count($values)) {
                $this->error(__METHOD__."\n\t".implode(',', $values));
                continue;
            }

            $fields = array_combine($columns, $values);

            list($m, $d, $c, $y) = str_split($fields['Shipment Date'], 2);
            $shipDate = "$c$y-$m-$d";

            $trackingNumber = $fields['Tracking #'];
            $orderId = $fields['References'];

            if (empty($orderId)) {
                continue;
            }

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
