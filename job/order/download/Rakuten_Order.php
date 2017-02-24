<?php

class Rakuten_Order extends OrderDownloader
{
    public function download()
    {
        $folder = Filenames::get('rakuten.us.order');

#       $client = new Marketplace\Rakuten\Client('US');
#       $client->downloadOrders($folder);

        $master = Filenames::get('rakuten.us.master.order');
        $this->genMasterOrderFile($folder, $master);
    }

    private function genMasterOrderFile($path, $masterFile)
    {
        $out = fopen($masterFile, 'w');

        $path = trim($path, '/');
        $files = glob("$path/23267604_*.*");

        $first = true;

        foreach ($files as $file) {
            // TODO: if file is too old, archive it

            $in = fopen($file, 'r');
            $title = fgets($in);

            if ($first) {
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
            if (strlen(trim($line)) > 0) { // skip blank line
                fputs($out, $line);
            }
        }
    }
}
