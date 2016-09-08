<?php

namespace Supplier\Ingram;

class Carrier
{
    static $carriers = [
        'A3' => 'PUROLATOR AIR 10 30',
        'A9' => 'PUROLATOR AIR 9AM',
        'AP' => 'APPS CARTAGE',
        'AT' => 'ATS TRANSPORT',
        'AW' => 'PUROLATOR AIR WEEKEND',
        'BO' => 'BACKORDER',
        'BT' => 'BYERS TRANSPORT',
        'CD' => 'F S CROSSDOCK',
        'CF' => 'CONFIGURATION LAB',
        'CT' => 'CONCORD',
        'DN' => 'DYNAMEX ND',
        'DR' => 'DAY AND ROSS',
        'DX' => 'DYNAMEX SD',
        'EN' => 'Expedited Parcel',
        'FN' => 'FASTAIR ND',
        'FS' => 'FASTAIR SD',
        'IX' => 'INGRAM EXPRESS',
        'L1' => 'LRM NOW',
        'L2' => 'LRM HOT',
        'L3' => 'LRM RUSH',
        'LA' => 'LOO AIR',
        'LG' => 'Lomis Ground',
        'M1' => 'MIDLAND COURIER',
        'MA' => 'MANITOULIN',
        'MC' => 'MIDLAND COURIER',
        'MR' => 'MEC RUSH',
        'MS' => 'MEC SUPERRUSH',
        'MT' => 'MIDLAND TRANSPORT',
        'OT' => 'OTHERS',
        'P3' => 'PUROLATOR 10 30',
        'P4' => 'PUROLATOR US BR 40',
        'P9' => 'PUROLATOR 9AM',
        'PA' => 'PUROLATOR AIR',
        'PI' => 'PUROLATOR GROUND',
        'PL' => 'PUROLATOR LATE',
        'PW' => 'PUROLATOR WEEKEND',
        'RG' => 'SEE ROUTING GUIDE',
        'S1' => 'SLH (ALBION)',
        'S2' => 'SLH (MONTREAL)',
        'S3' => 'SLH (UNDERHILL)',
        'S4' => 'SLH (HQPOW)',
        'S5' => 'SLH (SMART REGINA)',
        'S6' => 'SLH (SMART BELLEVLE)',
        'T7' => 'PREMIER 7AM',
        'T9' => 'PREMIER 8 30AM',
        'TP' => 'PREMIER 10 00AM',
        'TT' => 'TOTALLINE TRANSPORT',
        'U8' => 'UPS 8AM',
        'UC' => 'UPS PPC ONLY',
        'UG' => 'UPS GROUND',
        'UN' => 'UPS EXPRESS',
        'UR' => 'UPS RED',
        'WC' => 'WILL CALL',
        'WY' => 'PARCELWAY',
        'XN' => 'Xpresspost',
    ];

    public static function getName($code)
    {
        return isset(self::$carriers[$code]) ? self::$carriers[$code] : '';
    }

    public static function all()
    {
        return self::$carriers;
    }
}
