<?php

class Ingram_Tracking extends TrackingImporter
{
    public function import()
    {
        $filename = Filenames::get('ingram.tracking');

        if (!file_exists($filename)) {
            $this->error("File not found: $filename");
            return;
        }

        $fp = fopen($filename, 'r');

        $columns = [ 'shipDate', 'orderId', 'carrier', 'trackingNumber' ];

        while ($values = fgetcsv($fp)) {
            if (count($columns) != count($values)) {
                $this->error(__METHOD__ . print_r($values, true));
                continue;
            }

            $fields = array_combine($columns, $values);

            $this->saveToDb([
                'orderId'        => $fields['orderId'],
                'shipDate'       => $fields['shipDate'],
                'carrier'        => $fields['carrier'],
                'shipMethod'     => $fields['carrier'],
                'trackingNumber' => $fields['trackingNumber'],
                'sender'         => 'ING-DS',
            ]);
        }

        fclose($fp);
    }
}
