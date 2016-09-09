<?php

return array(
    \Supplier\ConfigKey::DH => array(
        'url'         => 'https://www.dandh.ca/dhXML/xmlDispatch',
        'username'    => '800712XML',
        'password'    => 'BTE@xml2013',
        'dropshippw'  => '',
        'partship'    => 'N',
        'backorder'   => 'N',
        'shipcarrier' => 'Purolator', // Pickup/Purolator/UPS
        'shipservice' => 'Ground', // Pickup/Ground/2nd Day Air/Next Day Air
        'onlybranch'  => '',  // Toronto
        'branches'    => '3', // 1-7
    ),

    \Supplier\ConfigKey::SYNNEX => array(
        'url'        => 'https://ec.synnex.ca/SynnexXML/PriceAvailability',
        'customerNo' => '1150897',
        'accountNo'  => 'UNKNOWN',
        'username'   => 'roy@btecanada.com',
        'password'   => 'Bte@sNx052016',
    ),

    \Supplier\ConfigKey::INGRAM => array(
        'url'      => 'https://newport.ingrammicro.com',
        'loginId'  => 'TrEv8fEbes',
        'password' => 'FVnbx25601',
        'autoRelease' => 'H', // 0,1
        'carrierCode' => 'PI',
        'backOrder' => 'Y',
        'splitShipment' => 'N',
        'splitLine' => 'N',
    ),

    \Supplier\ConfigKey::TECHDATA => array(
        'url'      => 'https://tdxml.techdata.com/xmlservlet',
        'username' => '567861',
        'password' => 'bteTDxml2014',
    ),

    \Supplier\ConfigKey::ASI => array(
        'url'      => 'https://www.asipartner.com/partneraccess/xml/price.asp',
        'CID'      => '75692',
        'CERT'     => '1FN9HRY3GDN5OMJ',
    ),
);
