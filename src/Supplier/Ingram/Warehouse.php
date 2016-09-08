<?php

namespace Supplier\Ingram;

class Warehouse
{
    // Canada
    const MISSISSAUGA = 40;
    const VANCOUVER   = 10;

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
            self::MISSISSAUGA => 'Mississauga',
            self::VANCOUVER   => 'Vancouver',
        ];
    }
}
