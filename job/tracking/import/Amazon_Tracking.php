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

        $columns = [
            'orderId',
            'status',
            'trackingNumber',
            'carrier',
            'shipDate',
        ];

        while (($values = fgetcsv($fp))) {
            if (count($columns) != count($values)) {
                $this->error(__METHOD__. print_r($values, true));
                continue;
            }
            $fields = array_combine($columns, $values);

            $this->saveToDb([
                'orderId'        => $fields['orderId'],
                'shipDate'       => $fields['shipDate'],
                'carrier'        => $fields['carrier'],
                'shipMethod'     => '',
                'trackingNumber' => $fields['trackingNumber'],
                'sender'         => $site,
            ]);
        }

        fclose($fp);
    }
}
