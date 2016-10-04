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
            $this->config = $config[strtoupper($site)];
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

        if ($this->site == 'CA') {
            $localFolder = 'E:/BTE/orders/rakuten/orders_ca/';
        } else if ($this->site == 'US') {
            $localFolder = 'E:/BTE/orders/rakuten/orders_us/';
        }

        $rakutenPrefix ='23267604_';

        foreach ($files as $file) {

            $localFile  = $localFolder . basename($file);

            if (preg_match("/$rakutenPrefix/i", $file) && !file_exists($localFile)) {
                echo "Downloading $file", PHP_EOL;
                $ftp->download("/Orders/$file", $localFile);
                $this->importFile($localFile);
            }
        }
    }

    protected function importFile($filename)
    {
        // TODO
    }
}
