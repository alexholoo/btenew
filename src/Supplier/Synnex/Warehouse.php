<?php

namespace Supplier\Synnex;

class Warehouse
{
    // Canada
    const HALIFAX  = 26;
    const GUELPH   = 29;
    const CALGARY  = 31;
    const MARKHAM  = 57;
    const RICHMOND = 60;

    public static function getName($code)
    {
        $warehouses = self::all();

        $name = "Warehouse($code)";
        if (isset($warehouses[$code])) {
            $name = $warehouses[$code];
        }

        return $name;
    }

    public static function getCode($name)
    {
        $warehouses = array_flip(self::all());

        $code = '';
        if (isset($warehouses[$name])) {
            $code = $warehouses[$name];
        }

        return $code;
    }

    public static function all()
    {
        return [
            self::HALIFAX  => 'Halifax, NS',
            self::GUELPH   => 'Guelph, ON',
            self::CALGARY  => 'Calgary, AB',
            self::MARKHAM  => 'Markham, ON',
            self::RICHMOND => 'Richmond, BC',
        ];
    }
}
