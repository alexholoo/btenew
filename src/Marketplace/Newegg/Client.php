<?php

namespace Marketplace\Newegg;

use Toolkit\File;
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

    public function downloadInventory($localFile)
    {
        echo "Start download inventory from Newegg ftp.", EOL;

        $ftp = new FtpClient($this->config);

        if (!$ftp->connect()) {
            echo "Failed to login Newegg {$this->site} FTP server", PHP_EOL;
            return;
        }

        $prefix = 'InventorySnapShot_A7BB_' . date('Ymd');

        $files = $ftp->listFiles('/Outbound/Inventory/');

        $zipfile = dirname($localFile) . '/newegg_listing_tmp.zip';

        foreach ($files as $file) {
            if (preg_match("/$prefix/i", $file)) {
                $ftp->download($file, $zipfile);
                break;
            }
        }

        File::unzip($zipfile);

        $unzipedFiles = glob(dirname($localFile) . "/$prefix*.CSV");

        foreach($unzipedFiles as $file){
            if (file_exists($localFile)) {
                unlink($localFile);
            }
            rename($file, $localFile);
            break;
        }

        echo "Successfully download inventory from Newegg ftp.", EOL;
    }

    public function uploadNewItems($localFile)
    {
        echo "Start uploading newitems to Newegg Canada ftp.", EOL;

        $ftp = new FtpClient($this->config);

        if (!$ftp->connect()) {
            echo "Failed to login Newegg {$this->site} FTP server", PHP_EOL;
            return;
        }

        $remoteFile = '/Inbound/CreateItem/'.basename($localFile);

        $ftp->upload($localFile, $remoteFile);

        echo "Successfully uploaded newitems to Newegg Canada ftp.", EOL;
    }
}
