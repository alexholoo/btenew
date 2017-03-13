<?php

//include '../public/init.php';
//include 'classes/Job.php';

//class Amazon_NewItems extends Job
class Amazon_NewItems extends NewItemsExporter
{
    public function export()
    {
        $this->exportNewItemsCA();
        $this->exportNewItemsUS();
    }

    protected function exportNewItemsCA()
    {
        $store = 'bte-amazon-ca';

        $listing = $this->loadListingCA();
        $blocked = $this->loadBlockedItemsCA();
        $skulist = $this->loadMasterSkuList();

        $newItems = $this->generateNewItems('CA', $skuList, $listing, $blocked);

        $this->saveNewItemsCA($newItems);
    }

    protected function exportNewItemsUS()
    {
        $store = 'bte-amazon-us';
        $filename = Filenames::get('amazon.us.newitems');

        $listing = $this->loadListingUS();
        $blocked = $this->loadBlockedItemsUS();
        $skulist = $this->loadMasterSkuList();

        $newItems = $this->generateNewItems('US', $skuList, $listing, $blocked);

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

    protected function loadMasterSkuList()
    {
        static $skuList = [];

        if ($skuList) {
            return $skuList;
        }

        $sql = "SELECT * FROM master_sku_list";
        $result = $this->db->fetchAll($sql);

        $skuList = array_column($result, null, 'SKU');

        return $skuList;
    }

    protected function generateNewItems($site, $skuList, $listing, $blocked)
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

                $upc = ltrim($result['UPC'], '0');
                $brand = $result['Manufacturer'];

                // skip item if in blocked item
                if (isset($blocked[$upc])) {
                    break;
                }

                $key = $strtolower($site) . '_amazon_blocked';
                $blocking = $result[$key]; //contain 'Y' if blocked

                $cost   = $result['best_cost'];
                $mpn    = $result['MPN'];
                $weight = $result['Weight'];
                $title  = $result['name'];

                $condition = $this->getCondition($title, $brand);

                if (!isset($listing[$sku]) && ($item['overall_qty'] > 0) && ($cost < 3000) && ($weight < 150)) {
                    $items[$sku] = [
                         'sku'       => $sku,
                         'cost'      => $cost,
                         'mpn'       => $mpn,
                         'upc'       => $upc,
                         'weight'    => $weight,
                         'blocking'  => $blocking,
                         'condition' => $condition,
                         'brand'     => $brand,
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

            $data = [
                $item['sku'],       // sku
                $item['upc'],       // upc
                '3',
                5555,               // temp price for new item
                4444,               // minimum price, use delete if not in use
                55555,              // maximum price, use delete if not in use
                $item['condition'], // condition
                '0',                // $qty
                'a',
                '22',               // ship international
                '20',               // express shipping available in canada
                $note,              // 'Fast Shipping from Canada', // note
                '',                 // fulfillment center id
            ];

            if ($item['blocking'] != 'Y') {
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

        foreach ($newItems as $item) {
            $price = ceil($item['cost'] * 1.5);
            $note = 'No Sales Tax. Part Number: '. $item['mpn'];

            $data = [
                $item['sku'],       // sku
                $item['upc'],       // upc
                '3',
                5555,               // temp price for new item
                4444,               // minimum price, use delete if not in use
                55555,              // maximum price, use delete if not in use
                $item['condition'], // condition
                '0',                // $qty
                'a',
                '2',                // Will ship in US only, no Canada (set in Amazon shipping rate)
                '',                 // expedited-shipping
                'n',                // standard-plus
                $note,              // note
                '',                 // fulfillment-center-id
                '',                 // product-tax-code
                '4',                // leadtime-to-ship
            ];

            if ($item['blocking'] != 'Y') {
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
