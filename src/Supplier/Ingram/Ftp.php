<?php

namespace Supplier\Ingram;

use Toolkit\File;
use Toolkit\FtpClient;

class Ftp
{
    protected static function connect()
    {
        $config = require APP_DIR . '/config/ftp.php';
        $account = $config['ftp']['ING'];

        $ftp = new FtpClient([
            'hostname' => $account['Host'],
            'username' => $account['User'],
            'password' => $account['Pass'],
        ]);

        if ($ftp->connect()) {
            return $ftp;
        }

        echo 'Cannot connect to ING FTP server', EOL;
        return false;
    }

    protected static function download($remoteFile, $localFile)
    {
        $ftp = self::connect();

        if ($ftp) {
            $ftp->download($remoteFile, $localFile);
            return true;
        }

        return false;
    }

    public static function getPricelist($localFile)
    {
        echo 'Downloading pricelist from ING FTP server', EOL;

        $remoteFile = '/FUSION/CA/BTECO/PRICE.ZIP';

        $folder = dirname($localFile);
        $zipfile = "$folder/ing-price.zip";

        self::download($remoteFile, $zipfile);

        File::unzip($zipfile);

        rename("$folder/PRICE.TXT", $localFile);
    }
}
