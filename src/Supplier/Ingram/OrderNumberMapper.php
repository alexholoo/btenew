<?php

namespace Supplier\Ingram;

use Phalcon\Di;

class OrderNumberMapper
{
    const TABLE = 'ingram_orderno_map';

    public static function getRealOrderNo($fake)
    {
        $db = Di::getDefault()->get('db');

        $table = self::TABLE;
        $sql = "SELECT real_orderno FROM $table WHERE fake_orderno='$fake' LIMIT 1";
        $result = $db->fetchOne($sql);

        if ($result) {
            return $result['real_orderno'];
        }

        return false;
    }

    public static function getFakeOrderNo($real)
    {
        $db = Di::getDefault()->get('db');

        $table = self::TABLE;

        $sql = "SELECT fake_orderno FROM $table WHERE real_orderno='$real' LIMIT 1";
        $result = $db->fetchOne($sql);
        if ($result) {
            return $result['fake_orderno'];
        }

        list($usec, $sec) = explode(" ", microtime());
        $fake = date('ymdhis') . substr($usec, 2, 4);

        $db->insertAsDict($table,
            [
                'real_orderno' => $real,
                'fake_orderno' => $fake,
            ]
        );

        return $fake;
    }
}
