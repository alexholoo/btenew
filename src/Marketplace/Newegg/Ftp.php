<?php

namespace Marketplace\Newegg;

use Toolkit\FtpClient;

class Ftp
{
    protected static function connect()
    {
        $config = require APP_DIR . '/config/newegg.php';
        $account = $config['ftp']['CA'];

        $ftp = new FtpClient($account);

        if ($ftp->connect()) {
            return $ftp;
        }

        echo 'Cannot connect to NeweggCA FTP server', EOL;
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

    public static function upload($localFile, $remoteFile)
    {
        $ftp = self::connect();

        if ($ftp) {
            $ftp->upload($localFile, $remoteFile);
            return true;
        }

        return false;
    }
}
