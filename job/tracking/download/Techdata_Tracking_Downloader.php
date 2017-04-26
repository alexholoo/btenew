<?php

use Supplier\Supplier;

class Techdata_Tracking_Downloader extends Tracking_Downloader
{
    public function run($argv = [])
    {
        try {
            $this->download();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function download()
    {
        $trackings = [];

        $client = Supplier::createClient('TD');

        $orders = $this->getDropshippedOrders('Techdata');

        foreach ($orders as $order) {
            $orderId = $order['orderId'];

            $result = $client->getOrderStatus($orderId);

            if ($result->trackingNumber) {
                $trackings[] = $result;
            }

            echo $orderId, ' ', $result->trackingNumber, EOL;
        }

        if ($trackings) {
            $filename = Filenames::get('techdata.tracking');

            $fp = fopen($filename, 'w');

            fputcsv($fp, [ 'orderId', 'trackingNumber', 'carrier', 'service', 'shipDate' ]);

            foreach ($trackings as $tracking) {
                fputcsv($fp, [
                    $tracking->orderNo,
                    $tracking->trackingNumber,
                    $tracking->carrier,
                    $tracking->service,
                    $tracking->shipDate,
                ]);
            }

            fclose($fp);
        }
    }
}
