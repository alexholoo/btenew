<?php

namespace Toolkit;

class OrderSerialNumber
{
    public function get($store)
    {
        $id = $this->getLastInsertId();

        $seq = str_pad($id % 100000, 5, '0', STR_PAD_LEFT);
        $date = date('ymd');

        return "$store-$date-$seq";
    }

    public function getLastInsertId()
    {
        $di = \Phalcon\Di::getDefault();
        $db = $di->get('db');

        $db->execute('INSERT INTO bte_order_seq VALUES ()');
        $id = $db->lastInsertId();

        return $id;
    }
}
