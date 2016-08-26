<?php

return array(
    \Supplier\ConfigKey::DH => array(
        'url'      => 'https://www.dandh.ca/dhXML/xmlDispatch',
        'username' => '800712XML',
        'password' => 'BTE@xml2013',
        'dropship' => 'DONTKNOW',
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
        'password' => 'YA2eQaThud',
    ),

    \Supplier\ConfigKey::TECHDATA => array(
        'url'      => 'https://tdxml.techdata.com/xmlservlet',
        'username' => '567861',
        'password' => 'bteTDxml2014',
    ),
);
