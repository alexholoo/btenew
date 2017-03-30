<?php

namespace Marketplace\Rakuten;

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
            $config = require APP_DIR . '/config/rakuten.php';
            $this->config = $config['ftp'][strtoupper($site)];
        }
    }

    public function downloadOrders($folder)
    {
        $ftp = new FtpClient($this->config);

        if (!$ftp->connect()) {
            $this->logger->error(__METHOD__ ." Failed to login Rakuten {$this->site} FTP server");
            return;
        }

        $files = $ftp->listFiles('/Orders/');
        if (!$files) {
            echo "No order files on Rakuten {$this->site} FTP server", PHP_EOL;
            return;
        }

        $rakutenPrefix ='23267604_';
        $localFolder = rtrim($folder, '/') . '/';

        foreach ($files as $file) {

            $localFile  = $localFolder . basename($file);

            if (preg_match("/$rakutenPrefix/i", $file) && !file_exists($localFile)) {
                echo "Downloading $file", PHP_EOL;
                $ftp->download("/Orders/$file", $localFile);
            }
        }
    }

    public function uploadTracking($localFile)
    {
        echo "Start uploading tracking to Rakuten ftp.", EOL;

        $ftp = new FtpClient($this->config);

        if (!$ftp->connect()) {
            $this->logger->error(__METHOD__ ." Failed to login Rakuten {$this->site} FTP server");
            return;
        }

        $remoteFile = '/Fulfillment/'.basename($localFile);

        $ftp->upload($localFile, $remoteFile);

        echo "Successfully uploaded tracking to Rakuten ftp.", EOL;
    }

    public function downloadInventory($localFile)
    {
        echo "Start downloading inventory from Rakuten ftp.", EOL;

        $ftp = new FtpClient($this->config);

        if (!$ftp->connect()) {
            $this->logger->error(__METHOD__ ." Failed to login Rakuten {$this->site} FTP server");
            return;
        }

        $remoteFile = '/Inventory/Archive/DetailedListingDownload.zip';
        $zipFile = dirname($localFile) . '/rakuten_listing_tmp.zip';

        $ftp->download($remoteFile, $zipFile);

        File::unzip($zipFile);

        $unzippedFile = dirname($localFile).'/DetailedListingDownload.txt';

        if (file_exists($unzippedFile)) {
            if (file_exists($localFile)) {
                unlink($localFile);
            }

            rename($unzippedFile, $localFile);
        }

        echo "Successfully downloaded inventory from Rakuten ftp.", EOL;
    }

    public function uploadNewItems($localFile)
    {
        echo "Start uploading newitems to Rakuten ftp.", EOL;

        $ftp = new FtpClient($this->config);

        if (!$ftp->connect()) {
            $this->logger->error(__METHOD__ ." Failed to login Rakuten {$this->site} FTP server");
            return;
        }

        $remoteFile = '/NewSku/'.basename($localFile);

        $ftp->upload($localFile, $remoteFile);

        echo "Successfully uploaded newitems to Rakuten ftp.", EOL;
    }
}
