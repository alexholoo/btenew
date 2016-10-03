<?php

namespace Marketplace\Newegg;

use Toolkit\FtpClient;

class Client
{
    protected $di;
    protected $db;
    protected $config;

    public function __construct($config = [])
    {
        // TODO: di & db
        $this->config = $config;
    }

    public function getOrders($days = 10)
    {
        $ftp = new FtpClient($this->config);

        $ftp->connect();

        $files = $ftp->listFiles('/Outbound/OrderList/');

		$localFolder = 'E:/BTE/orders/newegg/order_ca/';

        foreach ($files as $file) {

            $datetime = strtotime(preg_replace('/[^0-9]/', '', $file));
            if ((time() - $datetime) / (3600*24) > $days) { // 10 days old
                continue;
            }

            $localFile  = $localFolder . basename($file);

            if (!file_exists($localFile)) {
                echo "Downloading $file", PHP_EOL;
                $ftp->download($file, $localFile);
                $this->importFile($localFile);
            }
        }
    }

    protected function importFile($filename)
    {
        // TODO
    }
}

#include '../../../public/init.php';
#
#$config = include APP_DIR . '/config/newegg.php';
#$client = new Client($config['CA']);
#$client->getOrders();
