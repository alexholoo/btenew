<?php

namespace Supplier\Synnex;

use Toolkit\File;
use Toolkit\FtpClient;

class Ftp
{
    protected static function connect()
    {
        $config = require APP_DIR . '/config/ftp.php';
        $account = $config['ftp']['SYN'];

        $ftp = new FtpClient([
            'hostname' => $account['Host'],
            'username' => $account['User'],
            'password' => $account['Pass'],
        ]);

        if ($ftp->connect()) {
            return $ftp;
        }

        echo 'Cannot connect to Synnex FTP server', EOL;
        return false;
    }

    protected static function download($remoteFile, $localFile)
    {
        $ftp = self::connect();

        if ($ftp) {
            echo 'Downloading pricelist from Synnex FTP server', EOL;
            $ftp->download($remoteFile, $localFile);
            return true;
        }

        return false;
    }

    public static function getPricelist($localFile)
    {
        $folder = dirname($localFile);

        $zipfile = "$folder/syn-c1150897.zip";

        self::download('c1150897.zip', $zipfile);

        File::unzip($zipfile);

        rename("$folder/1150897.ap", $localFile);
    }

    public static function getTracking($folder)
    {
        $ftp = self::connect();

        if (!$ftp) {
            return false;
        }

        $files = $ftp->listFiles('.');

        foreach($files as $file) {
            if (preg_match("/BTE_COMPUTER_/i", $file)) {
                echo "Downloading $file ...", EOL;

                # old tracking files locate at
                # ./data/csv/amazon/synnex-tracking/*.xml

                $localFile = "$folder/$file";
                $ftp->download($file, $localFile);
                $ftp->deleteFile($file);
            }
        }

        return true;
    }
}

#include 'public/init.php';

#Ftp::getPricelist();
#Ftp::getTracking();
