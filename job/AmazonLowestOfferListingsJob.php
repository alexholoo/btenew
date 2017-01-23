<?php

include 'classes/Job.php';

class AmazonLowestOfferListingsJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $today = date('m-d-Y');

        $store = 'bte-amazon-ca';
        $input = 'w:/out/amazon_update/FBA-CA-restock.csv';
        $output ='w:/out/amazon_update/FBA-CA_lowest_price.csv';
        $output ='e:/FBA-CA_lowest_price.csv';
        $this->getLowestOfferListings($store, $input, $output);

        $store = 'bte-amazon-us';
        $input = 'w:/out/amazon_update/FBA-US-restock.csv';
        $output ='w:/out/amazon_update/FBA-US_lowest_price.csv';
        $output ='e:/FBA-US_lowest_price.csv';
        $this->getLowestOfferListings($store, $input, $output);
    }

    private function getLowestOfferListings($store, $inputFile, $outputFile)
    {
        if (!file_exists($inputFile)) {
            $this->log("File not found: $inputFile");
            return;
        }

        $this->log("Getting lowest offer listings: $outputFile");

        $fp = fopen($outputFile, 'w');

        $title = ['ASIN', 'Total Price', 'Listing Price', 'Shipping Price'];

        fputcsv($fp, $title);

        $asinchunk = $this->getAsinChunk($inputFile);

        foreach ($asinchunk as $asins) {

            $api = new AmazonProductInfo($store);
            $api->setASINs($asins);
            $api->setConditionFilter('New');
            $api->fetchLowestOffer();

            $products = $api->getProduct();
            //unset($products['AllOfferListingsConsidered']);
            if (!is_array($products)) {
                //pr($products);
                continue;
            }

            foreach ($products as $product) {
                if (!is_object($product)) {
                    //$product, EOL;
                    continue;
                }

                $data = $product->getData();
                //pr($data);

                $asin = $data['Identifiers']['MarketplaceASIN']['ASIN'];

                if (isset($data['LowestOfferListings'])) {
                    $total    = $data['LowestOfferListings'][0]['Price']['LandedPrice']['Amount'];
                    $listing  = $data['LowestOfferListings'][0]['Price']['ListingPrice']['Amount'];
                    $shipping = $data['LowestOfferListings'][0]['Price']['Shipping']['Amount'];

                    fputcsv($fp, [ $asin, $total, $listing, $shipping ]);
                } else {
                    //print_r($data);
                    //fputcsv($fp, [ $asin, '', '', '' ]);
                }
            }
        }

        fclose($fp);
    }

    private function getAsinChunk($filename)
    {
        $fp = fopen($filename, 'r');

        fgetcsv($fp); // skip first line

        $asins = [];

        while ($fields = fgetcsv($fp)) {
            $asins[] = $fields[0];
        }
 
        fclose($fp);

        return array_chunk($asins, 20);
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonLowestOfferListingsJob();
$job->run($argv);
