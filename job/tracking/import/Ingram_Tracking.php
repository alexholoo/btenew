<?php

class Ingram_Tracking extends TrackingImporter
{
    public function import()
    {
        $filename = Filenames::get('ingram.tracking');

        if (($fp = fopen($filename, 'r')) == false) {
            $this->error("File not found: $filename");
            return;
        }

        $columns = [ 'shipDate', 'orderId', 'carrier', 'trackingNumber' ];

        while ($fields = fgetcsv($fp)) {
            if (count($columns) != count($fields)) {
                $this->error(__METHOD__ . print_r($fields, true));
                continue;
            }

            $data = array_combine($columns, $fields);

            $this->saveToDb([
                'orderId'        => $data['orderId'],
                'shipDate'       => $data['shipDate'],
                'carrier'        => $data['carrier'],
                'shipMethod'     => $data['carrier'],
                'trackingNumber' => $data['trackingNumber'],
                'sender'         => 'ING-DS',
            ]);
        }

        fclose($fp);
    }
}
