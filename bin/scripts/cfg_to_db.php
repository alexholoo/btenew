<?php

require 'database.php';
include __DIR__ . '/../../vendor/autoload.php';

function importXmlConfig($db)
{
    $config = include __DIR__ . '/../../app/config/xmlapi.php';

    foreach ($config as $supplier => $items) {
        foreach ($items as $name => $value) {
            try {
                $db->insertAsDict('config', [
                    'supplier' => $supplier,
                    'section'  => 'xmlapi',
                    'name'     => $name,
                    'value'    => $value,
                    'desc'     => '',
                ]);
            } catch (Exception $e) {
                echo $e->getMessage(), EOL;
            }
        }
    }
}

function importFtpConfig($db)
{
    $config = include __DIR__ . '/../../job/config/config.php';

    unset($config['database']);
    $config = $config['ftp'];

    $namemap = [
        'DH'  => \Supplier\ConfigKey::DH,
        'DHU' => 'dhu',
        'SYN' => \Supplier\ConfigKey::SYNNEX,
        'SP'  => 'supercom',
        'ASI' => \Supplier\ConfigKey::ASI,
        'ASU' => 'asu',
        'TAK' => 'taknology',
        'ING' => \Supplier\ConfigKey::INGRAM,
    ];

    foreach ($config as $supplier => $items) {

        $supplier = $namemap[$supplier];

        $items['hostname'] = $items['Host'];
        $items['username'] = $items['User'];
        $items['password'] = $items['Pass'];
     
        $items['remotefile'] = $items['File'];
        $items['localfile'] = $items['Save'];
     
        unset($items['File']);
        unset($items['Save']);
        unset($items['Host']);
        unset($items['User']);
        unset($items['Pass']);

        foreach ($items as $name => $value) {
            try {
                $db->insertAsDict('config', [
                    'supplier' => $supplier,
                    'section'  => 'ftp',
                    'name'     => $name,
                    'value'    => $value,
                    'desc'     => '',
                ]);
            } catch (Exception $e) {
                echo $e->getMessage(), EOL;
            }
        }
    }
}

function importBteConfig($db)
{
    $bteInfo = [
        'name' => 'BTE Computer Inc',
        'contact' => 'Roy Zhang',
        'phone' => '',
        'email' => 'roy@btecanada.com',
        'address' => 'Unit 5, 270 Esna Park Dr',
        'zipcode' => 'L3R 1H3',
        'city' => 'Markham',
        'province' => 'ON',
        'country' => 'Canada',
    ];

    foreach ($bteInfo as $name => $value) {
        try {
            $db->insertAsDict('config', [
                'supplier' => 'bte',
                'section'  => 'info',
                'name'     => $name,
                'value'    => $value,
                'desc'     => '',
            ]);
        } catch (Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }
}

function loadConfig($db)
{
    static $config = [];

    if ($config) {
        return $config;
    }

    $sql = "SELECT * FROM config";
    $result = $db->query($sql);

    while ($row = $result->fetch()) {
        $section = $row['section'];
        $supplier = $row['supplier'];
        $name = $row['name'];
        $value = $row['value'];

        $config[$section][$supplier][$name] = $value;
    }

    return $config;
}

$db->execute('TRUNCATE TABLE config');

importBteConfig($db);
importXmlConfig($db);
importFtpConfig($db);

$cfg = loadConfig($db);
#print_r($cfg);

echo "DONE\n";
