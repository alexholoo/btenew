<?php

use Supplier\DH\Ftp;

class DH_Pricelist extends PricelistDownloader
{
    public function download()
    {
        $filename = Filenames::get('dh.pricelist');
        Ftp::getPricelist($filename);
    }
}
