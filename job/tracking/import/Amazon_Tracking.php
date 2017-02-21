<?php

class Amazon_Tracking extends TrackingImporter
{
    public function import()
    {
        $filename = Filenames::get('amazon.ca.tracking');
        $this->importTracking($filename, 'Amazon_CA_DS');

        $filename = Filenames::get('amazon.us.tracking');
        $this->importTracking($filename, 'Amazon_US_DS');
    }

    public function importTracking($filename, $site)
    {
        if (($fp = fopen($filename, 'r')) == false) {
            $this->error("File not found: $filename");
            return;
        }

        // 232076139,SHIPPED,7316971217505329,"Canada Post",2017-01-11

        while (($fields = fgetcsv($fp))) {
            $this->saveToDb([
                'orderId'        => $fields[0],
                'shipDate'       => $fields[4],
                'carrier'        => $fields[3],
                'shipMethod'     => '',
                'trackingNumber' => $fields[2],
                'sender'         => $site,
            ]);
        }

        fclose($fp);
    }
}
