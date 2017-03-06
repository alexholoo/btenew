<?php

class Bestbuy_Listing extends ListingImporter
{
    public function import()
    {
        $table = 'bestbuy_ca_listing';

        $filename = Filenames::get('bestbuy.listing');

        $listing = $this->csvToArray($filename);

        $this->db->execute("TRUNCATE TABLE $table");

        foreach ($listing as $offer) {
            try {
                $this->db->insertAsDict($table, [
                    'SKU'                  => $offer['shop_sku'],
                    'Product_ID'           => $offer['product_sku'],
                    'Category_Code'        => $offer['category_code'],
                    'Category_Label'       => $offer['category_label'],
                    'Product_Name'         => $offer['product_title'],
                    'Condition'            => 'New',
                    'Price'                => $offer['price'],
                    'Qty'                  => $offer['quantity'],
                    'Alert_Threshold'      => '',
                    'Logistic_Class'       => $offer['logistic_class'],
                    'Activated'            => $offer['active'] ? 'Y' : 'N',
                    'Available_Start_Date' => '',
                    'Available_End_Date'   => '',
                ]);
            } catch (Exception $e) {
                echo $e->getMessage(), EOL;
            }
        }
    }
}
