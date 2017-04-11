<?php

class Amazon_Shipment_Uploader extends Tracking_Uploader
{
    public function run($argv = [])
    {
        try {
            $this->upload();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function upload()
    {
        // Nothing to do, it's done in AmazonShippingUploadJob
        //
        // It's might be better to move AmazonShippingUploadJob here
        return;

        $store    = 'bte-amazon-ca';
        $orders   = $this->getUnshippedOrders($store);
        $filename = Filenames::get('amazon.ca.shipping');
        $this->uploadFeed($store, $filename);

        $store    = 'bte-amazon-us';
        $orders   = $this->getUnshippedOrders($store);
        $filename = Filenames::get('amazon.us.shipping');
        $this->uploadFeed($store, $filename);
    }

    private function uploadFeed($store, $file)
    {
        if (!IS_PROD) {
            throw new Exception('This script can only run on production server.');
        }

        if (!file_exists($file)) {
            //$this->error(__METHOD__." File not found: $file");
            return;
        }

        $this->log("Uploading shipping: $file");

        $feed = file_get_contents($file);

        $api = new AmazonFeed($store);
        $api->setFeedType('_POST_FLAT_FILE_FULFILLMENT_DATA_');
        $api->setFeedContent($feed);
        $api->submitFeed();

        $this->markOrdersShipped($file);

        File::backup($file);

       #$this->log(print_r($api->getResponse(), true));
    }

    protected function markOrdersShipped($filename)
    {
        $fp = fopen($filename, 'r');

        $columns = fgetcsv($fp, 0, "\t"); // skip first line

        $shipmentService = $this->di->get('shipmentService');

        while (($fields = fgetcsv($fp, 0, "\t"))) {
            $orderId = $fields[0];
            $shipmentService->markOrderAsShipped($orderId);
        }

        fclose($fp);
    }
}
