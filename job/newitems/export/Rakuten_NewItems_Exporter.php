<?php

class Rakuten_NewItems_Exporter extends NewItems_Exporter
{
    public function run($argv = [])
    {
        try {
            $this->export();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function export()
    {
        $listing = $this->loadListing();
        $blocked = $this->loadBlockedItems();
        $skulist = $this->loadMasterSkuList();

        $newItems = $this->generateNewItems($skulist, $listing, $blocked);

        $this->saveNewItems($newItems);
    }

    protected function loadListing()
    {
        $sql = "SELECT sku FROM rakuten_us_listing";
        $result = $this->db->fetchAll($sql);
        return array_column($result, 'Sku');
    }

    protected function loadBlockedItems()
    {
        return [];
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
                $brand = $item['MFR'];

                // skip item if in blocked item
                if (isset($blocked[$upc])) {
                    break;
                }

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
                         'condition'  => $condition,
                         'brand'      => $brand,
                    ];
                }
            }
        }

        return $newItems;
    }

    protected function saveNewItems($newItems)
    {
        $filename = Filenames::get('rakuten.us.newitems');

        $fp = fopen($filename, 'w+');

        $header = [
        ];

        fputcsv($fp, $header);

        foreach ($newItems as $item) {
            $data = [
            ];

            fputcsv($fp, $data);
        }

        fclose($fp);
    }
}
