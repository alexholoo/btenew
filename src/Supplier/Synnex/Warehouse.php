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
        switch($code) {
        case self::HALIFAX:
            $name = 'Halifax';
            break;
        case self::GUELPH:
            $name = 'Guelph';
            break;
        case self::CALGARY:
            $name = 'Calgary';
            break;
        case self::MARKHAM:
            $name = 'Markham';
            break;
        case self::RICHMOND:
            $name = 'Richmond BC';
            break;
        default:
            $name = "Warehouse($code)";
            break;
        }

        return $name;
        #eturn $name."($code)";
    }
}
