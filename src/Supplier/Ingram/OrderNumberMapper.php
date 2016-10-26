<?php

namespace Supplier\Ingram;

use Phalcon\Di;

class OrderNumberMapper
{
    const MAX_ORDERNO_LEN = 18;
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

        return $fake;
    }

    public static function getFakeOrderNo($real)
    {
        $db = Di::getDefault()->get('db');

        $table = self::TABLE;

        // if real order number logged, just return it, never log it again

        $sql = "SELECT fake_orderno FROM $table WHERE real_orderno='$real' LIMIT 1";
        $result = $db->fetchOne($sql);
        if ($result) {
            return $result['fake_orderno'];
        }

        // Max length of order number is 18 in Ingram, that's real bad.
        // Because the length of Amazon order number is 19, so we will
        // get an error "order number is too long", to avoid this, we
        // need to use fake order number instead.

        $fake = preg_replace('/[^0-9]/', '', $real);
        if (strlen($fake) >= self::MAX_ORDERNO_LEN) {
            list($usec, $sec) = explode(" ", microtime());
            $fake = date('ymdhis') . substr($usec, 2, 4);
        }

        // log real/fake order number to db for future use

        $db->insertAsDict($table, [
            'real_orderno' => $real,
            'fake_orderno' => $fake,
        ]);

        return $fake;
    }
}
