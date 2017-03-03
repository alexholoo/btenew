<?php

namespace Supplier\DH;

use Toolkit\FtpClient;

class Ftp
{
    protected static function connect()
    {
        $config = require APP_DIR . '/config/ftp.php';
        $account = $config['ftp']['DH'];

        $ftp = new FtpClient([
            'hostname' => $account['Host'],
            'username' => $account['User'],
            'password' => $account['Pass'],
        ]);

        if ($ftp->connect()) {
            return $ftp;
        }

        echo 'Cannot connect to DH FTP server', EOL;
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
        echo 'Downloading pricelist from DH FTP server', EOL;
        self::download('ITEMLIST', $localFile);
    }

    public static function getTracking($saveTo = null)
    {
        if (empty($saveTo)) {
            $saveTo = 'E:/BTE/tracking/dh/DH-TRACKING';
        }

        self::download('TRACKING', $saveTo);
    }
}

#include 'public/init.php';

#Ftp::getPricelist();
#Ftp::getTracking();
