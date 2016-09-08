<?php

namespace Supplier\Ingram;

class Warehouse
{
    // Canada
    const MISSISSAUGA = 40;
    const VANCOUVER   = 10;

    public static function getName($code)
    {
        switch($code) {
        case self::MISSISSAUGA:
            $name = 'Mississauga';
            break;
        case self::VANCOUVER:
            $name = 'Vancouver';
            break;
        default:
            $name = "Warehouse($code)";
            break;
        }

        return $name;
        #eturn $name."($code)";
    }
}
