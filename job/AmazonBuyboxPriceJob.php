<?php

include 'classes/Job.php';

class AmazonBuyboxPriceJob extends Job
{
    protected $store;
    protected $buyboxFilename;
    protected $fbaItemsFilename;

    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $this->store = 'bte-amazon-ca';
        $this->fbaItemsFilename = 'E:/BTE/amazon-ca-fba-items.txt';
        $this->buyboxFilename = 'W:/data/csv/amazon/amazon-ca-fba-buybox.csv';
        $this->genBuyboxPriceReport();

        $this->store = 'bte-amazon-us';
        $this->fbaItemsFilename = 'E:/BTE/amazon-us-fba-items.txt';
        $this->buyboxFilename = 'W:/data/csv/amazon/amazon-us-fba-buybox.csv';
        $this->genBuyboxPriceReport();
    }

    protected function genBuyboxPriceReport()
    {
        $result = $this->getBuyboxPrice();

        $filename = $this->getBuyboxFilename();
        $handle = fopen($filename, 'w');

        fputcsv($handle, ['sku', 'bte-price', 'buybox-price', 'bte-condition', 'condition', 'bte-lowest']);

        foreach ($result as $item) {
            fputcsv($handle, $item);
        }

        fclose($handle);
    }

    protected function getBuyboxPrice()
    {
        $fbaItems = $this->getFbaItems();

        $chunks = array_chunk($fbaItems, 20);

        $result = [];

        foreach ($chunks as $chunk) {
            $api = new \AmazonProductInfo($this->store);

            $api->setSKUs($chunk);
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

                $isLowest = false;
                $buyboxPrice = '-';
                $condition = '-';

                if (isset($data['CompetitivePricing']['CompetitivePrices'])) {
                    $info = current($data['CompetitivePricing']['CompetitivePrices']);
                    $isLowest = $info['belongsToRequester'];
                    $buyboxPrice = $info['Price']['LandedPrice']['Amount'];
                    $condition = $info['condition'];
                }

                $BTELowest = ($isLowest == 'true') ? 'Yes' : '';

                $result[$sku][0] = $sku;            // sku
                $result[$sku][1] = '';              // bte-price
                $result[$sku][2] = $buyboxPrice;    // buybox-price
                $result[$sku][3] = '';              // bte-condition
                $result[$sku][4] = $condition;      // condition
                $result[$sku][5] = $BTELowest;      // bte-lowest
            }

            // Get My Price and Condtion
            $api = new \AmazonProductInfo($this->store);

            $api->setSKUs($chunk);
            $api->fetchMyPrice();

            $products = $api->getProduct();

            foreach ($products as $product) {
                if (!is_object($product)) {
                    echo $product, EOL;
                    continue;
                }

                $data = $product->getData();

                //pr($data);

                if (!isset($data['Offers'])) {
                    continue;
                }

                $offers = $data['Offers'];

                foreach ($offers as $offer) {
                    $sku = $offer['SellerSKU'];
                    if (isset($result[$sku])) {
                        $price = $offer['RegularPrice']['Amount'];
                       #$price = $offer['BuyingPrice']['LandedPrice']['Amount'];
                        $condition = $offer['ItemCondition'];

                        $result[$sku][1] = $price;     // bte-price
                        $result[$sku][3] = $condition; // bte-condition
                    }
                }
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

        return $skulist;
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
        return $this->buyboxFilename;
    }

    protected function getReportFilename()
    {
        return $this->fbaItemsFilename;
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonBuyboxPriceJob();
$job->run($argv);
