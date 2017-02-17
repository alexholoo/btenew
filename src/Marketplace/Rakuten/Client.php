<?php

namespace Marketplace\Rakuten;

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
            $config = require APP_DIR . '/config/rakuten.php';
            $this->config = $config['ftp'][strtoupper($site)];
        }
    }

    public function getOrders()
    {
        $ftp = new FtpClient($this->config);

        if (!$ftp->connect()) {
            echo "Failed to login Rakuten {$this->site} FTP server", PHP_EOL;
            return;
        }

        $files = $ftp->listFiles('/Orders/');

        $localFolder = $this->getOrderFolder();

        $rakutenPrefix ='23267604_';

        foreach ($files as $file) {

            $localFile  = $localFolder . basename($file);

            if (preg_match("/$rakutenPrefix/i", $file) && !file_exists($localFile)) {
                echo "Downloading $file", PHP_EOL;
                $ftp->download("/Orders/$file", $localFile);
            }
        }
    }

    public function getOrderFolder()
    {
        if ($this->site == 'CA') {
            $folder = 'E:/BTE/orders/rakuten/orders_ca/';
        } else if ($this->site == 'US') {
            $folder = 'E:/BTE/orders/rakuten/orders_us/';
        }

        return $folder;
    }

    public function uploadTracking($localFile)
    {
        echo "Start uploading tracking to Rakuten ftp.", EOL;

        $ftp = new FtpClient($this->config);

        if (!$ftp->connect()) {
            echo "Failed to login Rakuten {$this->site} FTP server", EOL;
            return;
        }

        $remoteFile = '/Fulfillment/'.basename($localFile);

        $ftp->upload($localFile, $remoteFile);

        echo "Successfully uploaded tracking to Rakuten ftp.", EOL;
    }
}
