<?php

class Amazon_Tracking extends TrackingCollector
{
    public function collect()
    {
        $filename = 'E:/BTE/tracking/amazon/amazon_ca_dropship_tracking.csv';
        $this->collectTracking($filename, 'Amazon_CA_DS');

        $filename = 'E:/BTE/tracking/amazon/amazon_us_dropship_tracking.csv';
        $this->collectTracking($filename, 'Amazon_US_DS');
    }

    public function collectTracking($filename, $site)
    {
        if (($fp = fopen($filename, 'r')) == false) {
            $this->error("File not found: $filename");
            return;
        }

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
