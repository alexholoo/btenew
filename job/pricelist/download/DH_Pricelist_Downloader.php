<?php

use Toolkit\File;
use Supplier\DH\Ftp;

class DH_Pricelist_Downloader extends Pricelist_Downloader
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
        $filename = Filenames::get('dh.pricelist');

        if (File::expired($filename, '12 hours')) {
            Ftp::getPricelist($filename);
        }
    }
}
