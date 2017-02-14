<?php

namespace Supplier\Synnex;

use Toolkit\File;
use Toolkit\FtpClient;
use Supplier\DropshipTrackingLog;
use Supplier\Model\OrderStatusResult;

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

    public static function getPricelist()
    {
        $localFile = 'E:/BTE/pricelist/SYN-c1150897.zip';

        self::download('c1150897.zip', $localFile);

        File::unzip($localFile);

        $folder = dirname($localFile);
        rename("$folder/1150897.ap", "$folder/SYN-1150897.ap");
    }

    public static function getTracking()
    {
        $ftp = self::connect();

        if (!$ftp) {
            return false;
        }

        $files = $ftp->listFiles('.');

        foreach($files as $file) {
            if (preg_match("/BTE_COMPUTER_856.xml/i", $file)) {
                echo "Downloading $file ...", EOL;

                # old tracking files locate at
                # ./data/csv/amazon/synnex-tracking/*.xml

                $localFile = "E:/BTE/tracking/synnex/$file";
                $ftp->download($file, $localFile);
                #$ftp->deleteFile($file); // TODO; enable this

                //self::importTracking($localFile);
            }
        }

        return true;
    }

    // TODO: move this method to a better place, it should not be here
    public static function importTracking($file)
    {
        $fmtdate = function($date, $time) {
            return '20'.implode('-', str_split($date, 2)).' '.implode(':', str_split($time, 2));
        };

        $xml = simplexml_load_file($file);

        $result = new OrderStatusResult();

        $result->orderNo = (string)$xml->ShipNotice3D->PONumber;
        $result->trackingNumber = (string)$xml->ShipNotice3D->ShipTrackNo;
        $result->carrier = (string)$xml->ShipNotice3D->ShipDescription;
        $result->service = '';
        $result->shipDate = $fmtdate($xml->ShipNotice3D->ShipDate, $xml->ShipNotice3D->ShipTime);

        if ($result->trackingNumber) {
            echo $result->orderNo, ' ', $result->trackingNumber, EOL;
            #var_export($result);
            DropshipTrackingLog::save($result);
        }
    }
}

#include 'public/init.php';

#Ftp::getPricelist();
#Ftp::getTracking();
#Ftp::importTracking('E:/BTE/20161028130637837_BTE_COMPUTER_856.xml');  // 1 item in 1 package
#Ftp::importTracking('E:/BTE/20160714210556347_BTE_COMPUTER_856.xml');  // 13 items in 1 package
#Ftp::importTracking('E:/BTE/20160718150700317_BTE_COMPUTER_856.xml');  // 26 packages
#Ftp::importTracking('E:/BTE/20160715_000602057_BTE_COMPUTER_810.xml'); // Invoice, not shipment
