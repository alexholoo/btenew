<?php

namespace Marketplace\Newegg;

use Toolkit\FtpClient;

class Client
{
    protected $di;
    protected $db;
    protected $site;
    protected $config;

    public function __construct($site, $config = [])
    {
        // TODO: di & db
        $this->site = $site = strtoupper($site);

        if ($config) {
            $this->config = $config;
        } else {
            $config = require APP_DIR . '/config/newegg.php';
            $this->config = $config[strtoupper($site)];
        }
    }

    public function getOrders($days = 10)
    {
        $ftp = new FtpClient($this->config);

        if (!$ftp->connect()) {
            echo "Failed to login Newegg {$this->site} FTP server", PHP_EOL;
            return;
        }

        $files = $ftp->listFiles('/Outbound/OrderList/');

        if ($this->site == 'CA') {
            $localFolder = 'E:/BTE/orders/newegg/order_ca/';
        } else if ($this->site == 'US') {
            $localFolder = 'E:/BTE/orders/newegg/order_us/';
        }

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
