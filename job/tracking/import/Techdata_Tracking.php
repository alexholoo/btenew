<?php

class Techdata_Tracking extends TrackingImporter
{
    public function import()
    {
        $filename = 'W:/out/shipping/TD_tracking.csv';

        if (($fp = fopen($filename, 'r')) == false) {
            $this->error("File not found: $filename");
            return;
        }

        $columns = fgetcsv($fp);

        while ($fields = fgetcsv($fp)) {
            if (count($columns) != count($fields)) {
                $this->error(__METHOD__ . print_r($fields, true));
                continue;
            }

            $data = array_combine($columns, $fields);

            list($m, $d, $y) = explode('/', $data['Date']);
            $data['Date'] = "20$y-$m-$d";

            $this->saveToDb([
                'orderId'        => $data['PO #'],
                'shipDate'       => $data['Date'],
                'carrier'        => $data['Ship Method'],
                'shipMethod'     => $data['Ship Method'],
                'trackingNumber' => $data['Tracking'],
                'sender'         => 'TD-DS',
            ]);
        }

        fclose($fp);
    }
}
