<?php

use Toolkit\File;
use Supplier\DH\Ftp;

class DH_Pricelist extends PricelistDownloader
{
    public function download()
    {
        $filename = Filenames::get('dh.pricelist');

        if (File::expired($filename, '1 day')) {
            Ftp::getPricelist($filename);
        }
    }
}
