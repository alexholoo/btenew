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

        while ($fields = fgetcsv($fp)) {
            $this->saveToDb([
                'orderId'        => $fields[1],
                'shipDate'       => $fields[0],
                'carrier'        => $fields[2],
                'shipMethod'     => $fields[2],
                'trackingNumber' => $fields[3],
                'sender'         => 'ING-DS',
            ]);
        }

        fclose($fp);
    }
}
