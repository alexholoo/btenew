<?php

include __DIR__ . '/../public/init.php';

class InventoryBackupJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $this->backupInventory();
    }

    public function backupInventory()
    {
        $now = date('Ymd-His');
        $filename = "E:/BTE/data/backup/inventory/inventory-$now.csv";

        $fp = fopen($filename, 'w');

        $columns = $this->db->fetchAll('DESC bte_inventory');
        fputcsv($fp, array_column($columns, 'Field'));

        $rows = $this->db->fetchAll("SELECT * FROM bte_inventory");
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);
    }
}

$job = new InventoryBackupJob();
$job->run($argv);
