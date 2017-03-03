<?php

use Toolkit\File;
use Supplier\Ingram\Ftp;

class Ingram_Pricelist extends PricelistDownloader
{
    public function download()
    {
        $filename = Filenames::get('ingram.pricelist');

        if (File::expired($filename, '12 hours')) {
            Ftp::getPricelist($filename);
        }
    }
}
