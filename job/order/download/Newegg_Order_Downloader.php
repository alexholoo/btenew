<?php

class Newegg_Order_Downloader extends Order_Downloader
{
    public function run($argv = [])
    {
        try {
            $this->download();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function download()
    {
        // CA
        $folder = Filenames::get('newegg.ca.order');

        $client = new Marketplace\Newegg\Client('CA');
        $client->downloadOrders($folder);

        $master = Filenames::get('newegg.ca.master.order');
        $this->genMasterOrderFile($folder, $master);

        // US
        $folder = Filenames::get('newegg.us.order');

        $client = new Marketplace\Newegg\Client('US');
        $client->downloadOrders($folder);

        $master = Filenames::get('newegg.us.master.order');
        $this->genMasterOrderFile($folder, $master);
    }

    private function genMasterOrderFile($path, $masterFile)
    {
        $out = fopen($masterFile, 'w');

        $path = trim($path, '/');
        $files = glob("$path/OrderList_*.*");

        $first = true;

        foreach ($files as $file) {
            // if file is too old, archive it
            $datetime = strtotime(preg_replace('/[^0-9]/', '', $file));
            if ((time() - $datetime) / (3600*24) > 30) {
                rename($file, dirname($file).'/archive/'.basename($file));
                continue;
            }

            $in = fopen($file, 'r');
            $title = fgets($in);

            if ($first) {
                if (substr($title, 0, 3) == "\xEF\xBB\xBF") { // strip BOM
                    $title = substr($title, 3);
                }
                fputs($out, $title);
                $first = false;
            }

            $this->merge($in, $out);

            fclose($in);
        }

        fclose($out);
    }

    private function merge($in, $out)
    {
        while ($line = fgets($in)) {
            fputs($out, $line);
        }
    }
}
