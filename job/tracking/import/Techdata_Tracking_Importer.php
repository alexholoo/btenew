<?php

class Techdata_Tracking_Importer extends Tracking_Importer
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
        $filename = Filenames::get('techdata.tracking');

        if (!file_exists($filename)) {
            $this->error(__METHOD__." File not found: $filename");
            return;
        }

        $fp = fopen($filename, 'r');

        $columns = fgetcsv($fp);

        while ($values = fgetcsv($fp)) {
            if (count($columns) != count($values)) {
                $this->error(__METHOD__ . print_r($values, true));
                continue;
            }

            $fields = array_combine($columns, $values);

            $this->saveToDb([
                'orderId'        => $fields['orderId'],
                'shipDate'       => $fields['shipDate'],
                'carrierCode'    => $fields['carrier'],
                'carrierName'    => '',
                'shipMethod'     => $fields['service'],
                'trackingNumber' => $fields['trackingNumber'],
                'sender'         => 'TD-DS',
            ]);
        }

        fclose($fp);
    }
}
