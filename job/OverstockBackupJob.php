<?php

include __DIR__ . '/../public/init.php';

class OverstockBackupJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $this->backupOverstock();
    }

    public function backupOverstock()
    {
        $now = date('Ymd-His');
        $filename = "E:/BTE/data/backup/overstock/overstock-$now.csv";

        $fp = fopen($filename, 'w');

        $columns = $this->db->fetchAll('DESC overstock');
        fputcsv($fp, array_column($columns, 'Field'));

        $rows = $this->db->fetchAll("SELECT * FROM overstock");
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);
    }
}

$job = new OverstockBackupJob();
$job->run($argv);
