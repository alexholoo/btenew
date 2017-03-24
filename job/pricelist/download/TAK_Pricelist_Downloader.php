<?php

use Toolkit\File;
use Supplier\TAK\Ftp;

class TAK_Pricelist_Downloader extends Pricelist_Downloader
{
    public function download()
    {
        $filename = Filenames::get('tak.pricelist');

        if (File::expired($filename, '12 hours')) {
            Ftp::getPricelist($filename);
        }
    }
}

