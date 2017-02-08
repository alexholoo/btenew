<?php

function getBestbuyCategory($name)
{
    static $catmap = [
        'Motherboard' => 'CAT_10481',
        'Monitor' => 'CAT_1006',
        'Network Cable' => 'CAT_10494',
        'Scanner' => 'CAT_1012',
        'Inkjet&Printer' => 'CAT_10781',
        'OfficeJet' => 'CAT_10781',
        'Photo Printer' => 'CAT_30057',
        'Label Printer' => 'CAT_30057',
        'Laser&Printer' => 'CAT_26455',
        'HP&Laser' => 'CAT_26455',
        'GeForce' => 'CAT_25617',
        'Ethernet&Adapter' => 'CAT_19997',
        'Network Adapter' => 'CAT_19997',
        'HP&Notebook' => 'CAT_1002',
        'HP&Laptop' => 'CAT_1002',
        'IdeaPad' => 'CAT_1002',
        'NoteBook TP' => 'CAT_1002',

        'Mini PC' => 'CAT_1003',
        'Wireless&Router' => 'CAT_19994',
        'Switch' => 'CAT_10488',
        'Laptop Battery' => 'CAT_25595',
        'Toner Cartridge' => 'CAT_1171',
        'TONER CART' => 'CAT_1171',
        'CARTRIDGE' => 'CAT_1171',
        'Wireless Headset' => 'CAT_23274',
        'SPORT&HeadPhone' => 'CAT_321023',
        'HEADSET' => 'CAT_321023',
        'HEADPHONE' => 'CAT_321023',
        'In-Ear' => 'CAT_321023',
        'EARPHONE' => 'CAT_321023',
        'Keyboard and Mouse Combo' => 'CAT_14266',
        'Wireless&Mouse' => 'CAT_14266',
        'MOUSE' => 'CAT_14266',

        'GB&DDR&MHZ' => 'CAT_1076',

        'Speaker' => 'CAT_7675',
        'Desktop Computer' => 'CAT_1003',
        'Graphic&Card' => 'CAT_25617',
        'Video Card' => 'CAT_25617',
        'Multifunction Printer' => 'CAT_26455',
        'UTP CABLE' => 'CAT_10494',
        'C14 TO C13' => 'CAT_10494',
        'Cat6' => 'CAT_10494',
        'Network Patch Cable' => 'CAT_10494',
        'VGA CABLE' => 'CAT_10494',
        'RJ45 NETWORK CABLE' => 'CAT_10494',
        'RJ45' => 'CAT_10494',
        'CAT6E' => 'CAT_10494',
        'Cat5e' => 'CAT_10494',
        'C20 TO C19' => 'CAT_10494',
        'HDMI&CABLE' => 'CAT_10494',
        'USB&CABLE' => 'CAT_10494',
        'SNAGLESS&CABLE' => 'CAT_10494',
        'CABLE' => 'CAT_10494',
        'Audio Cable' => 'CAT_315699',

        'HDD' => 'CAT_1084',
        'SATA WD' => 'CAT_1084',
        'SATA SEAGATE' => 'CAT_1084',
        'Hard Disk Drive' => 'CAT_1084',
        'Hard Drive' => 'CAT_1084',
        'SSD' => 'CAT_1084',
        'Solid State Drive' => 'CAT_1084',
        'Power Supply' => 'CAT_10482',
        'Processor' => 'CAT_10483',
        'CPU INTEL' => 'CAT_10483',
        'CPU AMD' => 'CAT_10483',

        'PHOTO PAPER' => 'CAT_7673',
        'PROJECTOR' => 'CAT_1544',
        'EPSON' => 'CAT_26455',
        'CASIO&CALCULATOR' => 'CAT_1111',
    ];

    foreach ($catmap as $kwd => $cat) {
        if (Toolkit\Str::matchAll($kwd, $name)) {
            return $cat;
        }
    }

    return false;
}

if (1) {
    include __DIR__ . '/../public/init.php';

    $in = fopen('e:/BTE/import/master_sku_list.csv', 'r');
    $columns = fgetcsv($in);

    while (($fields = fgetcsv($in)) !== false) {
        $data = array_combine($columns, $fields);
        $name = $data['name'];
        $category = getBestbuyCategory($name);

        if (!$category) {
            echo $data['name'], EOL;
        }
    }
    fclose($in);
}
