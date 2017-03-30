<?php

namespace Marketplace\Newegg;

use Toolkit\File;
use Toolkit\FtpClient;

class Client
{
    protected $di;
    protected $db;
    protected $logger;
    protected $site;
    protected $config;

    public function __construct($site, $config = [])
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');

        $this->logger = $this->di->get('loggerService');

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
            $this->logger->error(__METHOD__ ." Failed to login Newegg {$this->site} FTP server");
            return;
        }

        $files = $ftp->listFiles('/Outbound/OrderList/');
        if (!$files) {
            echo "No order files on Newegg {$this->site} FTP server", PHP_EOL;
            return;
        }

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
            $this->logger->error(__METHOD__ ." Failed to login Newegg {$this->site} FTP server");
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
            $this->logger->error(__METHOD__ ." Failed to login Newegg {$this->site} FTP server");
            return;
        }

        $prefix = 'InventorySnapShot';
        $pattern = $prefix .'_...._'. date('Ymd');

        // 'InventorySnapShot_A7BB_' . date('Ymd'); // CA
        // 'InventorySnapShot_AD6H_' . date('Ymd'); // US

        $files = $ftp->listFiles('/Outbound/Inventory/');
        if (!$files) {
            echo "No inventory files on Newegg {$this->site} FTP server", PHP_EOL;
            return;
        }

        $zipfile = dirname($localFile) . "/newegg_{$this->site}_listing_tmp.zip";

        foreach ($files as $file) {
            if (preg_match("/$pattern/i", $file)) {
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
            $this->logger->error(__METHOD__ ." Failed to login Newegg {$this->site} FTP server");
            return;
        }

        $remoteFile = '/Inbound/CreateItem/'.basename($localFile);

        $ftp->upload($localFile, $remoteFile);

        echo "Successfully uploaded newitems to Newegg Canada ftp.", EOL;
    }
}
