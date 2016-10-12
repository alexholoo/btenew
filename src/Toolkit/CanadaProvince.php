<?php

namespace Toolkit;

class CanadaProvince
{
    static $names = array (
        'Alberta',
        'British Columbia',
        'Manitoba',
        'Saskatchewan',
        'Québec',
        'Quebec',
        'Nova Scotia',
        'Newfoundland',
        'New Brunsiwck',
        'Yukon',
        'Nunavut',
        'Prince Edward Island',
        'Ontario',
        'Northwest Territories'
    );

    static $codes = array ('AB','BC','MB','SK','QC','QC', 'NS','NL','NB','YT','NU','PE','ON','NT');

    public static function all()
    {
        return array_combine(self::$codes, self::$names);
    }

    public static function codeToName($code)
    {
        $map = array_combine(self::$codes, self::$names);
        $key = strtoupper($code);
        return isset($map[$key]) ? $map[$key] : $code;
    }

    public static function nameToCode($name)
    {
        $map = array_combine(self::$names, self::$codes);
        $key = preg_replace('/ {2,}|\s/', ' ', trim(ucwords(mb_strtolower($name))));
        return isset($map[$key]) ? $map[$key] : $name;
    }
}

//echo CanadaProvince::codeToName('qc');
//echo CanadaProvince::nameToCode('Québec');
//echo CanadaProvince::nameToCode(' british  columbia ');
//echo CanadaProvince::nameToCode(' nova   scotia ');
