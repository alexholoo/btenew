<?php

class AmazonBuyboxPriceJob
{
    protected $store = 'bte-amazon-ca';

    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');
    }

    public function run($argv = [])
    {
        $result = $this->getBuyboxPrice();

        $filename = $this->getBuyboxFilename();
        $handle = fopen($filename, 'w');
        fputcsv($handle, ['sku', 'buyboxPrice', 'condition', 'weAreLowest']);

        foreach ($result as $item) {
            fputcsv($handle, $item);
        }

        fclose($handle);
    }

    protected function getBuyboxPrice()
    {
        $fbaItems = $this->getFbaItems();

        $result = [];

        foreach ($fbaItems as $items) {
            $api = new \AmazonProductInfo($this->store);
            $api->setSKUs($items);
            $api->fetchCompetitivePricing();

            $products = $api->getProduct();

            if (!is_array($products)) {
                pr($products);
                continue;
            }

            foreach ($products as $product) {
                if (!is_object($product)) {
                    echo $product, EOL;
                    continue;
                }

                $data = $product->getData();

                $sku = $data['Identifiers']['SKUIdentifier']['SellerSKU'];

                $weAreLowest = false;
                $price = '-';
                $condition = '-';

                if (isset($data['CompetitivePricing']['CompetitivePrices'])) {
                    $info = current($data['CompetitivePricing']['CompetitivePrices']);
                    $weAreLowest = $info['belongsToRequester'];
                    $price = $info['Price']['LandedPrice']['Amount'];
                    $condition = $info['condition'];
                }

                $areWeLowest = ($weAreLowest == 'true') ? 'Yes' : '-';

                $result[] = [$sku, $price, $condition, $areWeLowest];
            }
        }

        return $result;
    }

    protected function getFbaItems()
    {
        $this->getInventoryReport();

        $filename = $this->getReportFilename();

        $handle = fopen($filename, 'r');

        $skus = [];

        while (($fields = fgetcsv($handle, 0, "\t"))) {
            $sku = $fields[0];
            $qty = $fields[5];

            if ($qty > 0) {
                $skus[] = $sku;
            }
        }

        fclose($handle);

        $skulist = array_unique($skus);

        echo "FBA items: ", count($skus), ' => ', count($skulist), EOL;

        return array_chunk($skulist, 20);
    }

    protected function getInventoryReport()
    {
        $filename = $this->getReportFilename();

        if (file_exists($filename) && time() - filemtime($filename) < 12*3600) {
            return;
        }

        echo 'Downloading FBA inventory report', EOL;

        $reportList = new \AmazonReportList($this->store);

        $reportList->setTimeLimits('-24 hours');
        $reportList->setReportTypes('_GET_AFN_INVENTORY_DATA_');
        $reportList->fetchReportList();

        $list = $reportList->getList();

        $reportId = $list[0]['ReportId'];

        $report = new \AmazonReport($this->store, $reportId);
        $report->fetchReport();
        $report->saveReport($filename);
    }

    protected function getBuyboxFilename()
    {
        return 'W:/data/csv/amazon/amazon-ca-fba-buybox.csv';
        return 'E:/BTE/amazon-ca-fba-buybox.csv';
    }

    protected function getReportFilename()
    {
        return 'E:/BTE/amazon-ca-fba-items.txt';
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonBuyboxPriceJob();
$job->run($argv);
