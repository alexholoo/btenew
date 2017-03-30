<?php

class eBay_Listing_Downloader extends Listing_Downloader
{
    public function download()
    {
        // BTE
        $client = new Marketplace\eBay\Client('gfs');
        $filename = Filenames::get('ebay.gfs.listing');
        $this->downloadListing($client, $filename);

        // ODO
        $client = new Marketplace\eBay\Client('odo');
        $filename = Filenames::get('ebay.odo.listing');
        $this->downloadListing($client, $filename);
    }

    private function downloadListing($client, $filename)
    {
        $fp = fopen($filename, 'w');

        fputcsv($fp, $this->getCsvHeader());

        $page = 1;
        $totalPages = 1;

        do {
            $res = $client->getMyeBaySelling($page++);
            if ($res->Ack == 'Success') {
                $totalPages = $res->ActiveList->PaginationResult->TotalNumberOfPages;
                $this->saveListing($fp, $res);
            }
        } while ($page <= $totalPages);

        fclose($fp);
    }

    private function saveListing($fp, $res)
    {
        $items = $res->ActiveList->ItemArray->Item;

        if ($items != null) {
            foreach ($items as $item) {
                fputcsv($fp, [
                    $item->SKU,
                    $item->ItemID,
                    $item->Title,
                    $item->SellingStatus->CurrentPrice,
                    $item->QuantityAvailable,
                ]);
            }
        }
    }

    private function getCsvHeader()
    {
        return [
            "SKU",
            "Item ID",
            "Title",
            "Fixed Price",
            "Quantity",
        ];
    }
}
