<?php

function getProductInfo($sku)
{
    $di = \Phalcon\Di::getDefault();
    $db = $di->get('db');

    $sql = "SELECT upc, mpn, name, manufacturer FROM master_sku_list WHERE sku='$sku'";
    $result = $db->fetchOne($sql);

    getBrand($result);

    return $result;
}

function getBrand($name)
{
    static $brands = [
         'TP-LINK',
         'D-Link',
         'Gigabyte',
         'SanDisk',
         'LG ',
         'Brother',
         'Logitech',
         'HP ',
         'Toshiba',
         'Acer',
         'TIZO',
         'ZOTAC ',
         'MEElectronics',
         'Xerox',
         'Eyefi',
         'SecurLink',
         'CONAIR',
         'AVERMEDIA',
         'SNAGLESS',
         'ASUS',
         'Lenovo',
         'ViewSonic',
         'MSI',
         'Targus',
         'Intel',
         'Azio ',
         'WD ',
         'Sony',
         'KINGSTON',
         'Seagate',
         'MATROX',
         'DELL',
         'SonicWALL',
         'Sapphire',
         'Monster',
         'Kensington',
         'Vantec',
         'CANON',
         'Epson',
         'OKI',
         'Creative',
         'SHARP',
         'Samsung',
         'BELKIN',
         'Casio',
         'MCAFEE',
         'JUNIPER',
         'SOLIDTEK',
         'ZWILLING',
         'ZOJIRUSHI',
         'VERTAGEAR',
         'Dolica',
         'Plantronics',
         'Hisense',
         'MAXELL',
         'ASROCK',
         'APEVIA',
         'AMETA',
         'CYBERPOWER',
         'Corsair',
         'Cisco',
         'CONAIR',
         'FUJITSU',
         'CUISINART',
         'PANASONIC',
         'FUJI',
         'AT&T',
         'TIGER',
         'BlackBerry',
         'OLYMPUS',
         'SecurLink',
         'MOTO ',
         'NETGEAR',
         'IOGEAR',
         'ioSafe',
         'zBoost',
         'xStack',
         'ZTE',
         'WorkFit',
         'WebSmart',
         'Web Smart',
         'Western Digital',
         'Tripp Lite',
         'Tenda Technology',
         'TallyGenicom',
         'Genicom',
         'Thermaltake',
         'Thermal',
         'WatchGuard',
         'WIREMOLD',
         'Swingline',
         'Supermicro',
         'StyleView',
         'Smartti',
         'Seasonic',
         'ScanSnap',
         'Noctua',
         'Arozzi',
         'Crucial',
         'LINKSYS',
         'JETWAY',
         'MARUSON',
         'ADATA',
         'Synology',
         'AcomData',
         'Microline',
         'Quantum',
         'IOMEGA',
         'Adaptec',
         'ZALMAN',
         'SteelSeries',
         'Radeon',
         'VISIONTEK',
         'Verbatim',
         'Survivor',
         'ProSafe',
         'Hawking Technolgoies',
         'iEssentials',
         'Lexar',
         'StarTech.com',
         'LightStream',
         'SmartRack',
         'TRENDnet',
         'NVIDIA',
         'KONICA',
         'Swiss Gear',
         '3M ',
         'APC ',
         'Ubiquiti',
         'Unitech',
         'Universal',
         'Optiplex',
         'MultiSync',
         'LaCie',
         'A-DATA Technology',
         'ARUBA',
         'BenQ',
         'Bentley',
         'Buffalo Technology',
         'BUFFALO',
         'C2G',
         'CAMPUS',
         'Edimax',
         'Matias',
         'Mediasonic',
         'Cooler Master',
         'DriveSmart',
         'Garmin',
         'Keytronic',
         'MEGABRAND',
         'ENERMAX',
         'PISEN',
         'Ricoh',
         'MAD CATZ',
         'ADESSO NUSCAN',
         'VitaSound',
         'Antec',
         'RIDATA',
         'Swordfish Tech',
         'PAPAGO',
         'Hewlett Packard',
         'ThinkServer' => 'Lenovo',
         'ThinkStation' => 'Lenovo',
         'ThinkPad' => 'Lenovo',
         'ThinkCentre' => 'Lenovo',
         'NoteBook TP' => 'Lenovo',
         'PIXMA' => 'Canon',
         'EliteDesk' => 'HP',
         'GIGA-BYTE' => 'GIGABYTE',
         'GIGA ' => 'GIGABYTE',
         'ZBOX' => 'ZOTAC',
    ];

    foreach ($brands as $key => $brand) {
        if (is_numeric($key)) {
            if (stripos($name, $brand) !== false) {
                return $brand;
            }
        } else {
            if (stripos($name, $key) !== false) {
                return $brand;
            }
        }
    }
}

if (1) {
    include __DIR__ . '/../public/init.php';

    $di = \Phalcon\Di::getDefault();
    $skuService = $di->get('skuService');

   #$in = fopen('w:/data/master_sku_list.csv', 'r');
    $in = fopen('e:/BTE/import/master_sku_list.csv', 'r');
    $columns = fgetcsv($in);
    $columns[1] = 'PN';

    while (($fields = fgetcsv($in)) !== false) {
        $data = array_combine($columns, $fields);
        $name = $data['name'];

        $brand = $skuService->getMfr($data['PN']);
        if (!$brand) {
            $brand = getBrand($name);
        }

        if (!$brand) {
            echo $data['name'], EOL;
        }
    }
    fclose($in);
}
