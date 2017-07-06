<?php

include __DIR__ . '/../public/init.php';

class OverstockBuyboxReportJob extends Job
{
    protected $items;

    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $this->items = $this->loadItems();

        $this->sellingPrice();
        $this->outputReport();
    }

    protected function sellingPrice()
    {
        $amazonService  = $this->di->get('amazonService');
        $neweggService  = $this->di->get('neweggService');
        $rakutenService = $this->di->get('rakutenService');
        $ebayService    = $this->di->get('ebayService');
        $orderService   = $this->di->get('orderService');

        foreach ($this->items as $key => $item) {
            $sku = $item['sku'];

            // Amazon CA/US
            $listing = $amazonService->findSku($sku, 'CA');
            if ($listing) {
                $this->items[$key]['amazon_ca'] = $listing['price'];
            }

            $listing = $amazonService->findSku($sku, 'US');
            if ($listing) {
                $this->items[$key]['amazon_us'] = $listing['price'];
            }

            // Newegg CA/US
            $listing = $neweggService->findSku($sku, 'CA');
            if ($listing) {
                $this->items[$key]['newegg_ca'] = $listing['selling_price'];
            }

            $listing = $neweggService->findSku($sku, 'US');
            if ($listing) {
                // Price is not available in NeweggUS listing
                $this->items[$key]['newegg_us'] = $this->items[$key]['amazon_us']; // *1.03
            }

           #$listing = $rakutenService->findSku($sku);
           #if ($listing) {
           #    $this->items[$key]['rakuten'] = $listing['Price'];
           #}

            $qty = $orderService->countOrdersBySku($sku);
            if ($qty) {
                $this->items[$key]['sold'] = $qty;
            }

            $buybox = $this->getBuyBoxPrices($sku);
            if ($buybox) {
                $this->items[$key]['buybox_ca'] = $buybox['ca'];
                $this->items[$key]['buybox_us'] = $buybox['us'];
            }
        }
    }

    protected function getBuyBoxPrices($sku)
    {
        $buyboxPrices = [ 'ca' => '', 'us' => '' ];

        $sql = "SELECT * FROM amazon_ca_buybox_report_selleractive WHERE sku='$sku'";
        $info = $this->db->fetchOne($sql);

        if ($info) {
            $buyboxPrices['ca'] = $info['buybox_price'];
        }

        $sql = "SELECT * FROM amazon_us_buybox_report_selleractive WHERE sku='$sku'";
        $info = $this->db->fetchOne($sql);

        if ($info) {
            $buyboxPrices['us'] = $info['buybox_price'];
        }

        return $buyboxPrices;
    }

    protected function outputReport()
    {
        $filename = 'E:/BTE/overstock-buybox-report.csv';
        $columns = [ 'SKU', 'Title', 'Condition', 'Cost', 'Qty', 'Amazon CA', 'Amazon US', 'Newegg CA', 'Newegg US', 'Buybox CA', 'Buybox US', 'QtySoldIn30Days' ];

        $fp = fopen($filename, 'w');

        fputcsv($fp, $columns);

        foreach ($this->items as $item) {
            fputcsv($fp, $item);
        }

        fclose($fp);
    }

    protected function loadItems()
    {
        $overstock = $this->loadOverstock();
        $inventory = $this->loadInventory();

        $all = array_merge($overstock, $inventory);

        usort($all, function($a, $b) {
            if ($a['qty'] == $b['qty']) {
                return ($a['cost'] > $b['cost']) ? -1 : 1;
            }
            return ($a['qty'] > $b['qty']) ? -1 : 1;
        });

        return $all;
    }

    protected function loadOverstock()
    {
        $dbname = "z:/BTE-Price-List/bte-dataprocess-files.accdb";

        $dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ=$dbname;";
        $accdb = new PDO($dsn);

        $sql = "SELECT * FROM [overstock]";
        $rows = $accdb->query($sql);

        $records = [];
        foreach ($rows as $row) {
            $sku = $row['SKU Number'];
            if (substr($sku, 0, 1) == '*') {
                continue;
            }
            $records[] = [
                'sku'       => $sku,
                'title'     => $row['Title'],
                'condition' => $row['condition'],
                'cost'      => $row['cost'],
                'qty'       => $row['Actual Quantity'],
               #'upc'       => $row['UPC Code'],
               #'mpn'       => $row['MPN'],
                'amazon_ca' => '',
                'amazon_us' => '',
                'newegg_ca' => '',
                'newegg_us' => '',
               #'rakuten'   => '',
               #'ebay'      => '',
                'buybox_ca' => '',
                'buybox_us' => '',
                'sold'      => '',
            ];
        }
        return $records;
    }

    protected function loadInventory()
    {
        $dbname = "z:/BTE-Price-List/bte-inventory.accdb";

        $dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ=$dbname;";
        $accdb = new PDO($dsn);

        $sql = "SELECT * FROM [bte-inventory]";
        $rows = $accdb->query($sql);

        $records = [];
        foreach ($rows as $row) {
            $sku = $row['Part Number'];
            if (substr($sku, 0, 1) == '*') {
                continue;
            }
            if ($row['PurchasePrice'] < 10) {
                continue;
            }
            $records[] = [
                'sku'       => $sku,
                'title'     => $row['Title '], // NOTICE the trailing space
                'condition' => $row['Condition'],
                'cost'      => $row['PurchasePrice'],
                'qty'       => $row['QtyOnHand'],
               #'upc'       => $row['UPC Code'],
               #'mpn'       => $row['MPN'],
                'amazon_ca' => '',
                'amazon_us' => '',
                'newegg_ca' => '',
                'newegg_us' => '',
               #'rakuten'   => '',
               #'ebay'      => '',
                'buybox_ca' => '',
                'buybox_us' => '',
                'sold'      => '',
            ];
        }

        return $records;
    }
}

$job = new OverstockBuyboxReportJob();
$job->run($argv);
