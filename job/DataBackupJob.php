<?php

include __DIR__ . '/../public/init.php';

class DataBackupJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $this->backup();
    }

    public function backup()
    {
        $this->backupInventory();
        $this->backupOverstock();
    }

    public function backupInventory()
    {
        $now = date('Ymd-His');
        $filename = "E:/BTE/data/backup/inventory/inventory-$now.csv";

        $this->backupTable('bte_inventory', $filename);
    }

    public function backupOverstock()
    {
        $now = date('Ymd-His');
        $filename = "E:/BTE/data/backup/overstock/overstock-$now.csv";

        $this->backupTable('overstock', $filename);
    }

    protected function backupTable($table, $filename)
    {
        if (!($fp = fopen($filename, 'w'))) {
            $this->error(__METHOD__." Failed to create file $filename");
            return;
        }

        $this->log("=> Backup $table");

        $columns = $this->db->fetchAll("DESC $table");
        fputcsv($fp, array_column($columns, 'Field'));

        $rows = $this->db->fetchAll("SELECT * FROM $table");
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);
    }
}

$job = new DataBackupJob();
$job->run($argv);
