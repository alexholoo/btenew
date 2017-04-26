<?php

use Supplier\Supplier;

class ASI_Tracking_Downloader extends Tracking_Downloader
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

        $client = Supplier::createClient('AS');

        $orders = $this->getDropshippedOrders('ASI');

        foreach ($orders as $order) {
            $orderId = $order['orderId'];

            $result = $client->getOrderStatus($orderId);

            if ($result->trackingNumber) {
                $trackings[] = $result;
            }

            echo $orderId, ' ', $result->trackingNumber, EOL;
        }

        if ($trackings) {
            $filename = Filenames::get('asi.tracking');

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
