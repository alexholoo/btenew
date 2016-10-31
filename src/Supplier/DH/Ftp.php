<?php

namespace Supplier\DH;

use Toolkit\FtpClient;
use Supplier\DropshipTrackingLog;
use Supplier\Model\OrderStatusResult;

class Ftp
{
    protected static function download($remoteFile, $localFile)
    {
        $config = require APP_DIR . '/config/ftp.php';
        $account = $config['ftp']['DH'];

        $ftp = new FtpClient([
            'hostname' => $account['Host'],
            'username' => $account['User'],
            'password' => $account['Pass'],
        ]);

        if ($ftp->connect()) {
            $ftp->download($remoteFile, $localFile);
        }
    }

    public static function getPricelist()
    {
        self::download('ITEMLIST', 'E:\BTE\DH-ITEMLIST');
    }

    public static function getTracking()
    {
        #$columns = [
        #    // 0,    1,             2,           3,              4,          5,
        #    [ 'H1', 'OrderNo',     'InvoiceNo', 'InvoiceTotal' ],
        #    [ 'D1', 'TrackingNum', 'Carrier',   'ServiceLevel', 'ShipMode', 'DateShipped' ],
        #    [ 'D2', 'ModelNo',     'Qty',       'SerialNo+',    'Price' ],
        #];

        $localFile = 'E:/BTE/DH-TRACKING';

        self::download('TRACKING', $localFile);

        $fmtdate = function($str) {
            return substr($str, 4).'-'.substr($str, 0, 2).'-'.substr($str, 2, 2);
        };

        // import to dropship_tracking
        $fp = fopen($localFile, 'r');
        while ($fields = fgetcsv($fp, 0, '|')) {
            if ($fields[0] == 'H1') {
                $fields = array_map('trim', $fields);

                $result = new OrderStatusResult();
                $result->orderNo = $fields[1];

                $fields = fgetcsv($fp, 0, '|');

                if ($fields[0] == 'D1') {
                    $fields = array_map('trim', $fields);

                    $result->trackingNumber = $fields[1];
                    $result->carrier = $fields[2]. ' ' .$fields[3];
                    $result->service = $fields[4];
                    $result->shipDate = $fmtdate($fields[5]);
                }

                if ($result->trackingNumber) {
                    echo $result->orderNo, ' ', $result->trackingNumber, EOL;
                    #var_export($result);
                    DropshipTrackingLog::save($result);
                }
            }
        }

        fclose($fp);
    }
}

#include 'public/init.php';

#Ftp::getPricelist();
#Ftp::getTracking();
