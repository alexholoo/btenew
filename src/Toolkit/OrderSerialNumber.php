<?php

namespace Toolkit;

class OrderSerialNumber
{
    public static function get($store)
    {
        $di = \Phalcon\Di::getDefault();
        $db = $di->get('db');

        $db->execute('INSERT INTO bte_order_seq VALUES ()');
        $id = $db->lastInsertId();

        $seq = str_pad($id % 100000, 5, '0', STR_PAD_LEFT);
        $date = date('ymd');

        return "$store-$date-$seq";
    }
}

#include 'public/init.php';
#
#var_dump(OrderSerialNumber::get('ACA'));
#var_dump(OrderSerialNumber::get('AUS'));
#var_dump(OrderSerialNumber::get('ECA'));
#var_dump(OrderSerialNumber::get('EUS'));
