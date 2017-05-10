<?php

include __DIR__ . '/../public/init.php';

use Toolkit\File;

class OverstockAddJob extends Job
{
    public function run($args = [])
    {
        $this->log('>> '. __CLASS__);

        $filename = $args[1];

        if (!file_exists($filename)) {
            echo "$filename not found\n";
            return;
        }

        $fp = fopen($filename, 'r');
        $columns = fgetcsv($fp);

        $overstockService = $this->di->get('overstockService');

        while ($values = fgetcsv($fp)) {
            $fields = array_combine($columns, $values);
            $overstockService->add($fields);
        }

        fclose($fp);

        File::backup($filename);
    }
}

$job = new OverstockAddJob();
$job->run($argv);
