<?php

include 'classes/Job.php';

class TrackingImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $this->importTrackings();
    }

    protected function importTrackings()
    {
        $this->importAmazonCA();
        $this->importAmazonUS();
        $this->importNeweggCA();
        $this->importEbay();
        $this->importRakuten();
        $this->importShippingEasy();
        $this->importMasterShipment(); // keep this as last one?
    }

    protected function importAmazonCA()
    {
        $filename = 'w:/out/shipping/amazon_ca_shipment.txt';

        $this->log("Importing $filename");

        $fp = fopen($filename, 'r');
        fgetcsv($fp); // skip title

        while ($fields = fgetcsv($fp, 0, "\t")) {
            $order_id    = $fields[0];
            $carrier     = $fields[4];
            $trackingnum = $fields[6];
            $site        = $fields[7];
            $shipdate    = $fields[3];
            $source      = 'amazon_ca';

            if ($carrier == 'Other') {
                $carrier = $fields[5];
            }
	
            $this->insertToDb([
                $order_id,
                $carrier,
                $trackingnum,
                $site,
                $shipdate,
                $source,
            ]);
        }

        fclose($fp);
    }

    protected function importAmazonUS()
    {
        $filename = 'w:/out/shipping/amazon_us_shipment.csv';

        $this->log("Importing $filename");

        $fp = fopen($filename, 'r');
        fgetcsv($fp); // skip title

        while ($fields = fgetcsv($fp)) {
            $order_id    = $fields[0];
            $carrier     = $fields[4];
            $trackingnum = $fields[6];
            $site        = $fields[7];
            $shipdate    = $fields[3];
            $source      = 'amazon_us';

            if ($carrier == 'Other') {
                if ($fields[5] == 'Ship From Canada:UPS') {
                    $carrier = substr($fields[5], 17);
                    $trackingnum = substr($fields[6], 15);
                }	
                if ($fields[5] == 'TNT'){
                    if (isset($fields[9])){
                        $carrier = 'Fedex';
                        $trackingnum = $fields[9];
                    }
                    else {
                        $carrier = $fields[5];
                        $trackingnum = $fields[6];
                    }
                }
            }

            $this->insertToDb([
                $order_id,
                $carrier,
                $trackingnum,
                $site,
                $shipdate,
                $source,
            ]);
        }
        fclose($fp);
    }

    protected function importNeweggCA()
    {
        $filename = 'w:/out/shipping/newegg_canada_tracking.csv';

        $this->log("Importing $filename");

        $fp = fopen($filename, 'r');
        fgetcsv($fp); // skip title

        while ($fields = fgetcsv($fp)) {
            $order_id    = $fields[0];
            $carrier     = $fields[29];
            $trackingnum = $fields[31];
            $site        = $fields[2];
            $shipdate    = $fields[28];
            $source      = 'newegg_ca';

            $this->insertToDb([
                $order_id,
                $carrier,
                $trackingnum,
                $site,
                $shipdate,
                $source,
            ]);
        }

        fclose($fp);
    }

    protected function importEbay()
    {
        $filename = 'w:/out/shipping/ebay_orders_archive.csv';

        $this->log("Importing $filename");

        $fp = fopen($filename, 'r');
        fgetcsv($fp); // skip title

        while ($fields = fgetcsv($fp)) {
            $order_id    = $fields[0];
            $carrier     = $fields[6];
            $trackingnum = $fields[22];
            $site        = 'ebay';
            $shipdate    = $fields[3];
            $source      = 'ebay';

            $this->insertToDb([
                $order_id,
                $carrier,
                $trackingnum,
                $site,
                $shipdate,
                $source,
            ]);
        }

        fclose($fp);
    }

    protected function importRakuten()
    {
        $filename = 'w:/out/shipping/rakuten_tracking.txt';

        $this->log("Importing $filename");

        $fp = fopen($filename, 'r');
        fgetcsv($fp);

        while ($fields = fgetcsv($fp, 0, "\t")) {
            $order_id    = $fields[0];
            $carrier     = $fields[3];
            $trackingnum = $fields[4];
            $site        = 'Rakuten';
            $shipdate    = $fields[5];
            $source      = 'Rakuten';

            list($m, $d, $y) = explode('/', $shipdate);
            $shipdate = "$y-$m-$d";

            $this->insertToDb([
                $order_id,
                $carrier,
                $trackingnum,
                $site,
                $shipdate,
                $source,
            ]);
        }

        fclose($fp);
    }

    protected function importMasterShipment()
    {
        $filename = 'w:/out/shipping/master_shipment.txt';

        $this->log("Importing $filename");

        $fp = fopen($filename, 'r');
        fgetcsv($fp);

        while ($fields = fgetcsv($fp, 0, "\t")) {
            $order_id    = $fields[0];
            $carrier     = $fields[4];
            $trackingnum = $fields[6];
            $site        = 'master_shipment';
            $shipdate    = $fields[3];
            $source      = 'master_shipment';

            if ($carrier == 'Other') {
                $carrier = $fields[5];
            }

            $this->insertToDb([
                $order_id,
                $carrier,
                $trackingnum,
                $site,
                $shipdate,
                $source,
            ]);
        }

        fclose($fp);
    }

    protected function importShippingEasy()
    {
        $filename = 'w:/out/shipping/shippingeasy-shipping-report.csv';

        $this->log("Importing $filename");

        $fp = fopen($filename, 'r');

        // skip the first few lines
        fgetcsv($fp); fgetcsv($fp); fgetcsv($fp);
        fgetcsv($fp); fgetcsv($fp); fgetcsv($fp);

        while ($fields = fgetcsv($fp)) {
            $order_id    = $fields[5];
            $carrier     = $fields[12];
            $trackingnum = ltrim($fields[23], "'");
            $site        = 'shippingeasy';
            $shipdate    = $fields[0];
            $source      = 'shippingeasy';

            $this->insertToDb([
                $order_id,
                $carrier,
                $trackingnum,
                $site,
                $shipdate,
                $source,
            ]);
        }

        fclose($fp);
    }

    protected function insertToDb($fields)
    {
        try {
            $success = $this->db->insertAsDict('master_tracking', [
                'order_id'    => $fields[0],
                'carrier'     => $fields[1],
                'trackingnum' => $fields[2],
                'site'        => $fields[3],
                'shipdate'    => $fields[4],
                'source'      => $fields[5],
            ]);
        } catch (Exception $e) {
            // echo $e->getMessage(), EOL;
        }
    }
}

include __DIR__ . '/../public/init.php';

$job = new TrackingImportJob();
$job->run($argv);
