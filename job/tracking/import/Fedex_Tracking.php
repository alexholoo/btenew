<?php

class Fedex_Tracking extends TrackingImporter
{
    public function import()
    {
        return;

        $filename = Filenames::get('fedex.tracking');

        if (!file_exists($filename)) {
            $this->error(__METHOD__." File not found: $filename");
            return;
        }

        $fp = fopen($filename, 'r');

        while (($fields = fgetcsv($fp)) !== FALSE) {

        }

        fclose($fp);
    }
}
