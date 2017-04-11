<?php

class Amazon_Tracking_Importer extends Tracking_Importer
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
        $filename = Filenames::get('amazon.ca.tracking');
        $this->importTracking($filename, 'Amazon_CA_DS');

        $filename = Filenames::get('amazon.us.tracking');
        $this->importTracking($filename, 'Amazon_US_DS');
    }

    public function importTracking($filename, $site)
    {
        if (!file_exists($filename)) {
            $this->error(__METHOD__." File not found: $filename");
            return;
        }

        $fp = fopen($filename, 'r');

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
                'carrierCode'    => $fields['carrier'],
                'carrierName'    => '',
                'shipMethod'     => '',
                'trackingNumber' => $fields['trackingNumber'],
                'sender'         => $site,
            ]);
        }

        fclose($fp);
    }
}
