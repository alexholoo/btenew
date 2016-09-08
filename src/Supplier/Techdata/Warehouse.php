<?php

namespace Supplier\Techdata;

class Warehouse
{
    public static function getName($code)
    {
        $warehouses = self::all();

        $name = $code;

        if (isset($warehouses[$code])) {
            $name = $warehouses[$code];
        }

        return $name;
    }

    public static function all()
    {
        return [
            'A1' => 'Mississauga', // ON
            'A2' => 'Richmond', // BC
            '99' => 'Vendor Drop-Ship/Software Licensing',
        ];
    }
}
