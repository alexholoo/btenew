<?php

//include '../public/init.php';
//include 'classes/Job.php';

//class Amazon_NewItems extends Job
class Amazon_NewItems_Exporter extends NewItems_Exporter
{
    public function run($argv = [])
    {
        $this->export();
    }

    public function export()
    {
        $this->exportNewItemsCA();
        $this->exportNewItemsUS();
    }

    protected function exportNewItemsCA()
    {
        $listing = $this->loadListingCA();
        $blocked = $this->loadBlockedItemsCA();
        $skulist = $this->loadMasterSkuList();

        $newItems = $this->generateNewItems($skulist, $listing, $blocked);

        $this->saveNewItemsCA($newItems);
    }

    protected function exportNewItemsUS()
    {
        $listing = $this->loadListingUS();
        $blocked = $this->loadBlockedItemsUS();
        $skulist = $this->loadMasterSkuList();

        $newItems = $this->generateNewItems($skulist, $listing, $blocked);

        $this->saveNewItemsUS($newItems);
    }

    protected function loadListingCA()
    {
        $sql = "SELECT sku FROM amazon_ca_listings";
        $result = $this->db->fetchAll($sql);
        return array_column($result, 'sku');
    }

    protected function loadListingUS()
    {
        $sql = "SELECT sku FROM amazon_us_listings";
        $result = $this->db->fetchAll($sql);
        return array_column($result, 'sku');
    }

    protected function loadBlockedItemsCA()
    {
        $sql = "SELECT sku_ca sku, upc, mpn FROM amazon_blocked_items";
        $result = $this->db->fetchAll($sql);
        return array_column($result, null, 'upc');
    }

    protected function loadBlockedItemsUS()
    {
        $sql = "SELECT sku_us sku, upc, mpn FROM amazon_blocked_items";
        $result = $this->db->fetchAll($sql);
        return array_column($result, null, 'upc');
    }

    protected function generateNewItems($skuList, $listing, $blocked)
    {
        $newItems = [];

        $PNs = [ 'syn_pn', 'td_pn', 'ing_pn', 'dh_pn', 'asi_pn', 'tak_pn', 'ep_pn', 'BTE_PN' ];

        foreach ($skuList as $item) {
            foreach ($PNs as $PN) {
                if ($item[$PN] == '') {
                    continue;
                }

                $sku = strtoupper($item[$PN]);
                $prefix = substr($sku, 0, 3);

                // skip disabled item
                if ($prefix == '***') {
                    break;
                }

                $upc = ltrim($item['UPC'], '0');
                $brand = $item['Manufacturer'];

                // skip item if in blocked item
                if (isset($blocked[$upc])) {
                    break;
                }

                $blockedCA = $item['ca_amazon_blocked'];
                $blockedUS = $item['us_amazon_blocked'];

                $cost   = $item['best_cost'];
                $mpn    = $item['MPN'];
                $weight = $item['Weight'];
                $title  = $item['name'];

                $condition = $this->getCondition($title, $brand);

                if (!isset($listing[$sku]) && ($item['overall_qty'] > 0) && ($cost < 3000) && ($weight < 150)) {
                    $items[$sku] = [
                         'sku'        => $sku,
                         'cost'       => $cost,
                         'mpn'        => $mpn,
                         'upc'        => $upc,
                         'weight'     => $weight,
                         'ca_blocked' => $blockedCA,
                         'us_blocked' => $blockedUS,
                         'condition'  => $condition,
                         'brand'      => $brand,
                    ];
                }
            }
        }

        return $newItems;
    }

    protected function getCondition($title, $brand)
    {
        $condition = '11'; // new

        if (stristr($title, 'refur')       || stristr($title, 'ref') ||
            stristr($title, 'Recertified') || stristr($brand, 'refur')) {
            $condition = '10';
        }
        else if (stristr($title, 'open box')) {
            $condition = '1';  // used like new
        }
        else if (stristr($title, 'used')) {
            $condition = '2';  // used very good
        }

        return $condition;
    }

    protected function saveNewItemsCA($newItems)
    {
        $filename = Filenames::get('amazon.ca.newitems');

        $fp = fopen($filename, 'w+');

        $header = [
            'sku',
            'product-id',
            'product-id-type',
            'price',
            'minimum-seller-allowed-price',
            'maximum-seller-allowed-price',
            'item-condition',
            'quantity',
            'add-delete',
            'will-ship-internationally',
            'expedited-shipping',
            'item-note',
            'fulfillment-center-id'
        ];

        fputcsv($fp, $header, "\t");

        foreach ($newItems as $item) {
            $price =  ceil($item['cost'] * 1.5);
            $note = 'GST/HST Only, No PST. Part Number: '. $item['mpn'];

            $tmpPrice  = 5555;
            $minPrice  = 4444;
            $maxPrice  = 55555;
            $condition = $item['condition'];
            $qty       = 0;

            $data = [
                $item['sku'],       // sku
                $item['upc'],       // upc
                '3',
                $tmpPrice,          // temp price for new item
                $minPrice,          // minimum price, use delete if not in use
                $maxPrice,          // maximum price, use delete if not in use
                $condition,         // condition
                $qty,               // qty
                'a',
                '22',               // ship international
                '20',               // express shipping available in canada
                $note,              // 'Fast Shipping from Canada', // note
                '',                 // fulfillment center id
            ];

            if ($item['ca_blocked'] != 'Y') {
                fputcsv($fp, $data, "\t");
            }
        }

        fclose($fp);
    }

    protected function saveNewItemsUS($newItems)
    {
        $filename = Filenames::get('amazon.us.newitems');

        $fp = fopen($filename, 'w+');

        $header = [
            'sku',
			'product-id',
            'product-id-type',
            'price',
            'minimum-seller-allowed-price',
            'maximum-seller-allowed-price',
            'item-condition',
            'quantity',
            'add-delete',
            'will-ship-internationally',
            'expedited-shipping',
            'standard-plus',
            'item-note',
            'fulfillment-center-id',
            'product-tax-code',
            'leadtime-to-ship'
        ];

        fputcsv($fp, $header, "\t");

        foreach ($newItems as $item) {
            $price = ceil($item['cost'] * 1.5);
            $note  = 'No Sales Tax. Part Number: '. $item['mpn'];

            $tmpPrice  = 5555;
            $minPrice  = 4444;
            $maxPrice  = 55555;
            $condition = $item['condition'];
            $qty       = 0;

            $data = [
                $item['sku'],       // sku
                $item['upc'],       // upc
                '3',
                $tmpPrice,          // temp price for new item
                $minPrice,          // minimum price, use delete if not in use
                $maxPrice,          // maximum price, use delete if not in use
                $condition,         // condition
                $qty,               // $qty
                'a',
                '2',                // Will ship in US only, no Canada (set in Amazon shipping rate)
                '',                 // expedited-shipping
                'n',                // standard-plus
                $note,              // note
                '',                 // fulfillment-center-id
                '',                 // product-tax-code
                '4',                // leadtime-to-ship
            ];

            if ($item['us_blocked'] != 'Y') {
                fputcsv($fp, $data, "\t");
            }
        }

        fclose($fp);
    }
}

//include 'newitems/Filenames.php';
//
//$job = new Amazon_NewItems();
//$job->export();
