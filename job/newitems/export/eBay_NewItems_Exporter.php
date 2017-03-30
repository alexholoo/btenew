<?php

class eBay_NewItems_Exporter extends NewItems_Exporter
{
    public function export()
    {
        $this->exportNewItemsGFS();
        $this->exportNewItemsODO();
    }

    protected function exportNewItemsGFS()
    {
        $listing = $this->loadListingGFS();
        $blocked = $this->loadBlockedItemsGFS();
        $skulist = $this->loadMasterSkuList();

        $newItems = $this->generateNewItems($skulist, $listing, $blocked);

        $this->saveNewItemsGFS($newItems);
    }

    protected function exportNewItemsODO()
    {
        $listing = $this->loadListingODO();
        $blocked = $this->loadBlockedItemsODO();
        $skulist = $this->loadMasterSkuList();

        $newItems = $this->generateNewItems($skulist, $listing, $blocked);

        $this->saveNewItemsODO($newItems);
    }

    protected function loadListingGFS()
    {
        $sql = "SELECT sku FROM amazon_ca_listings";
        $result = $this->db->fetchAll($sql);
        return array_column($result, 'sku');
    }

    protected function loadListingODO()
    {
        $sql = "SELECT sku FROM amazon_us_listings";
        $result = $this->db->fetchAll($sql);
        return array_column($result, 'sku');
    }

    protected function loadBlockedItemsGFS()
    {
        $sql = "SELECT sku_ca sku, upc, mpn FROM amazon_blocked_items";
        $result = $this->db->fetchAll($sql);
        return array_column($result, null, 'upc');
    }

    protected function loadBlockedItemsODO()
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

                $blockedCA = $item['ca_ebay_blocked'];
                $blockedUS = $item['us_ebay_blocked'];

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

    protected function saveNewItemsGFS($newItems)
    {
        $filename = Filenames::get('ebay.gfs.newitems');

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

    protected function saveNewItemsODO($newItems)
    {
        $filename = Filenames::get('ebay.odo.newitems');

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
