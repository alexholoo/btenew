<?php

use Toolkit\File;
use Supplier\Synnex\Ftp;

class Synnex_Pricelist extends PricelistDownloader
{
    public function download()
    {
        $filename = Filenames::get('synnex.pricelist');

        if (File::expired($filename, '12 hours')) {
            Ftp::getPricelist($filename);
        }
    }
}
