<?php

use Toolkit\File;
use Supplier\Ingram\Ftp;

class Ingram_Pricelist_Downloader extends Pricelist_Downloader
{
    public function download()
    {
        $filename = Filenames::get('ingram.pricelist');

        if (File::expired($filename, '12 hours')) {
            Ftp::getPricelist($filename);
        }
    }
}
