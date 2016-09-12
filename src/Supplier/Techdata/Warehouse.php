<?php

namespace Supplier\Techdata;

class Warehouse
{
    const MISSISSAUGA = 'A1'; 
    const RICHMOND    = 'A2'; 

    public static function getName($code)
    {
        $warehouses = self::all();

        $name = $code;

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
            self::MISSISSAUGA => 'MISSISSAUGA, ON',
            self::RICHMOND    => 'RICHMOND, BC',
        #   '99' => 'Vendor Drop-Ship/Software Licensing',
        ];
    }
}
