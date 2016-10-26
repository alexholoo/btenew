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

        $localFolder = $this->getOrderFolder();

        foreach ($files as $file) {

            $datetime = strtotime(preg_replace('/[^0-9]/', '', $file));
            if ((time() - $datetime) / (3600*24) > $days) { // 10 days old
                continue;
            }

            $localFile  = $localFolder . basename($file);

            if (!file_exists($localFile)) {
                echo "Downloading $file", PHP_EOL;
                $ftp->download($file, $localFile);
            }
        }
    }

    public function getOrderFolder()
    {
        if ($this->site == 'CA') {
            $folder = 'E:/BTE/orders/newegg/orders_ca/';
        } else if ($this->site == 'US') {
            $folder = 'E:/BTE/orders/newegg/orders_us/';
        }

        return $folder;
    }

    public function getTrackingFile()
    {
        if ($this->site == 'CA') {
            $file = 'E:/BTE/newegg_canada_tracking.csv';
        } else if ($this->site == 'US') {
            $file = 'E:/BTE/newegg_usa_tracking.csv';
        }

        return $file;
    }

    public function uploadTracking()
    {
        echo "Start uploading tracking to Newegg Canada ftp.", EOL;

        $ftp = new FtpClient($this->config);

        if (!$ftp->connect()) {
            echo "Failed to login Newegg {$this->site} FTP server", PHP_EOL;
            return;
        }

        // TODO: generate 'E:/BTE/newegg_canada_tracking.csv'
        // TrackingFile

        $localFile  = $this->getTrackingFile();
        $remoteFile = './Inbound/Shipping/newegg_canada_tracking.csv';

        $ftp->upload($localFile, $remoteFile);

        echo "Successfully uploaded tracking to Newegg Canada ftp.", EOL;
    }
}
