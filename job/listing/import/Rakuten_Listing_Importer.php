<?php

class Rakuten_Listing_Importer extends Listing_Importer
{
    public function import()
    {
        $table = 'rakuten_us_listing';
        $filename = Filenames::get('rakuten.us.listing');

        $this->importListings($filename, $table);
    }

    private function importListings($file, $table)
    {
        //$this->log("Importing $file");

        $listings = $this->getListings($file);

        if (!$listings) {
            return;
        }

        $this->db->execute("TRUNCATE TABLE $table");

        $columns = $this->getColumns();

        $chunks = array_chunk($listings, 2000);
        foreach ($chunks as $chunk) {
            try {
                $sql = $this->genInsertSql($table, $columns, $chunk);
                $this->db->execute($sql);
            } catch (\Exception $e) {
                $this->error($e->getMessage() .' in '. __METHOD__);
            }
        }
    }

    private function getListings($file)
    {
        $listings = [];

        if (!file_exists($file)) {
            //$this->log("Failed to open file: $fname");
            return $listings;
        }

        $fh = fopen($file, 'rb');

        $title = fgetcsv($fh, 0, "\t");

        while (($fields = fgetcsv($fh, 0, "\t"))) {
            $listings[] = $fields;
        }

        fclose($fh);

        return $listings;
    }

    private function getColumns()
    {
        return [
            'ListingId',
            'ReferenceId', // !!
            'Title',
            'ItemConditionId',
            'ItemCondition',
            'ListingStatusId',
            'ListingStatusName',
            'Quantity',
            'Price',
            'MAP',
            'MapTypeId',
            'MapTypeName',
            'OriginalPrice',
            'PriceReferenceId',
            'OfferExpeditedShipping',
            'OfferTwoDayShipping',
            'OfferOneDayShipping',
            'ShippingRateStandard',
            'ShippingRateExpedited',
            'ShippingRateTwoDay',
            'ShippingRateOneDay',
            'ShippingLeadTime',
            'Sku', // !!
            'DateModified',
            'SellerSku',
            'MSRP',
            'Weight',
            'TaxonomyCategoryId',
            'TaxonomyCategoryPath',
            'PartitionID',
            'PartitionName',
            'UPC',
            'MfgName',
            'MfgPartNo',
        ];
    }

    /**
     * For reference only
     */
    private function getCsvHeader()
    {
        return [
            'ListingId',
            'Sku',
            'Title',
            'ItemConditionId',
            'ItemCondition',
            'ListingStatusId',
            'ListingStatusName',
            'Quantity',
            'Price',
            'MAP',
            'MapTypeId',
            'MapTypeName',
            'OriginalPrice',
            'PriceReferenceId',
            'OfferExpeditedShipping',
            'OfferTwoDayShipping',
            'OfferOneDayShipping',
            'ShippingRateStandard',
            'ShippingRateExpedited',
            'ShippingRateTwoDay',
            'ShippingRateOneDay',
            'ShippingLeadTime',
            'ReferenceId',
            'DateModified',
            'SellerSku',
            'MSRP',
            'Weight',
            'TaxonomyCategoryId',
            'TaxonomyCategoryPath',
            'PartitionID',
            'PartitionName',
            'UPC',
            'MfgName',
            'MfgPartNo',
        ];
    }
}
