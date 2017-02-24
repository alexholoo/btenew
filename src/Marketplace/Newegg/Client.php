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
            $this->config = $config['ftp'][strtoupper($site)];
        }
    }

    public function downloadOrders($folder, $days = 10)
    {
        $ftp = new FtpClient($this->config);

        if (!$ftp->connect()) {
            echo "Failed to login Newegg {$this->site} FTP server", PHP_EOL;
            return;
        }

        $files = $ftp->listFiles('/Outbound/OrderList/');

        $localFolder = rtrim($folder, '/') . '/';

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

    public function uploadTracking($localFile)
    {
        echo "Start uploading tracking to Newegg Canada ftp.", EOL;

        $ftp = new FtpClient($this->config);

        if (!$ftp->connect()) {
            echo "Failed to login Newegg {$this->site} FTP server", PHP_EOL;
            return;
        }

        $remoteFile = '/Inbound/Shipping/'.basename($localFile);

        $ftp->upload($localFile, $remoteFile);

        echo "Successfully uploaded tracking to Newegg Canada ftp.", EOL;
    }
}
