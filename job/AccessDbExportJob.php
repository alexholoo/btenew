<?php

include __DIR__ . '/../public/init.php';

class AccessDbExportJob extends Job
{
    protected $info = [
        [
            'dbname'   => '/BTE-Price-List/bte-inventory.accdb',
            'sql'      => 'SELECT * FROM [bte-inventory];',
            'csvtitle' => ["Part Number","Title","Selling Cost","Type","FBA_allocation","Notes","PurchasePrice","Condition","QtyOnHand","Weight(lbs)","UPC Code","Mfr_Name","MPN","Length(inch)","Width(inch)","Depth(inch)","total","serialnumber"],
            'csvfile'  => '/data/csv/bte-inventory.csv',
        ],
        [
            'dbname'   => '/BTE-Price-List/bte-dataprocess-files.accdb',
            'sql'      => 'SELECT * FROM [amazon_blocked_items];',
            'csvtitle' => ["us_sku","UPC","MPN","ca_sku","row_number","Notes","Field1"],
            'csvfile'  => '/data/amazon_blocked_items.csv',
        ],
        [
            'dbname'   => '/BTE-Price-List/bte-dataprocess-files.accdb',
            'sql'      => 'SELECT * FROM [skip];',
            'csvtitle' => ["Part Number","Notes","USA_Floor","CA_Floor","Title","Cost", "Condition","MPN","Source","Source-SKU","row_number","UPC Code", "total","MPN1","ID1"],
            'csvfile'  => '/data/skip.csv',
        ],
        [
            'dbname'   => '/BTE-Price-List/bte-dataprocess-files.accdb',
            'sql'      => 'SELECT * FROM [overstock];',
            'csvtitle' => ["SKU","Title","cost","condition","quantity","Allocation", "Quantity","MPN","note","UPC Code","Weight(lbs)","row_number"],
            'csvfile'  => '/data/overstock.csv',
        ],
        [
            'dbname'   => '/BTE-Price-List/bte-dataprocess-files.accdb',
            'sql'      => 'SELECT * FROM [MAP];',
            'csvtitle' => ["Part Number","Mpart","Title","MAP(US)", "MAP(CA)","Notes1","Notes2","row_number"],
            'csvfile'  => '/data/MAP.csv',
        ],
        [
            'dbname'   => '/BTE-Price-List/bte-dataprocess-files.accdb',
            'sql'      => 'SELECT * FROM [blocked_brands];',
            'csvtitle' => ["brand","us-amazon_blocked_brand","ca-amazon_blocked_brand", "ca_eBay_blocked_brand","us_eBay_blocked_brand", "uk-amazon_blocked_brand","row_number"],
            'csvfile'  => '/data/blocked_brands.csv',
        ],
        [
            'dbname'   => '/BTE-Price-List/bte-dataprocess-files.accdb',
            'sql'      => 'SELECT * FROM [Newegg_NewListing_Blocked];',
            'csvtitle' => ["row_number","UPC","Part Number","Notes"],
            'csvfile'  => '/data/Newegg_NewListing_Blocked.csv',
        ],
        [
            'dbname'   => '/BTE-Price-List/bte-dataprocess-files.accdb',
            'sql'      => 'SELECT * FROM [Rakuten_wrong_item];',
            'csvtitle' => ["row_number","SKU","UPC","Notes"],
            'csvfile'  => '/data/Rakuten_wrong_item.csv',
        ],
        [
            'dbname'   => '/BTE-Price-List/bte-dataprocess-files.accdb',
            'sql'      => 'SELECT * FROM [oversized_sku];',
            'csvtitle' => ["SKU","Weight(lb)","Length(in)","Width(in)","Height(in)","UPC","Title","ID"],
            'csvfile'  => '/data/sku_by_truck.csv',
        ],
        [
            'dbname'   => '/BTE-Price-List/bte-dataprocess-files.accdb',
            'sql'      => 'SELECT * FROM [SBN_Stock];',
            'csvtitle' => ["SKU","Qty","SellPrice","ID"],
            'csvfile'  => '/data/sbn_canada.csv',
        ],
        [
            'dbname'   => '/BTE-Price-List/bte-dataprocess-files.accdb',
            'sql'      => 'SELECT * FROM [Promotions];',
            'csvtitle' => ["SKU","TITLE","start_date","end_date","Amazon_CA","Amazon_US","Ebay_US","NG_CA","NG_US","BUY_PROMO","Memo","ID"],
            'csvfile'  => '/data/promotions.csv',
        ],
        [
            'dbname'   => '/Purchasing/General Purchase.accdb',
            'sql'      => 'SELECT * FROM [Newegg];',
            'csvtitle' => [],
            'csvfile'  => '/data/csv/newegg.csv',
        ],
    ];

    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $this->exportAccess();
    }

    protected function exportAccess()
    {
        $folder = 'E:/BTE'; // W: for prod

        foreach ($this->info as $info) {
            $dbname   = $info['dbname'];
            $sql      = $info['sql'];
            $csvtitle = $info['csvtitle'];
            $csvfile  = $info['csvfile'];

            $accdb = $this->openAccessDB("Z:$dbname");

            try {
                $result = $accdb->query($sql);
                $rows = $result->fetchAll(PDO::FETCH_ASSOC);

                if (!($fp = fopen($folder.$csvfile, 'w+'))) {
                    $this->error("Failed to create file $folder$csvfile");
                    continue;
                }

                if ($csvtitle) {
                    fputcsv($fp, $csvtitle);
                }

                foreach ($rows as $row) {
                    fputcsv($fp, $row);	
                }

                fclose($fp);

                $this->log("$folder$csvfile exported");

            } catch (PDOExepction $e) {
                echo $e->getMessage, EOL;
            }
        }
    }

    protected function openAccessDB($dbname)
    {
       #$dbname = "Z:/Purchasing/General Purchase.accdb";

        $dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ=$dbname;";
        $db = new PDO($dsn);

        return $db;
    }
}

$job = new AccessDbExportJob();
$job->run($argv);
