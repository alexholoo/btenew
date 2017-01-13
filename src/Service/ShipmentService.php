<?php

namespace Service;

use Phalcon\Di\Injectable;

class ShipmentService extends Injectable
{
    public function getMasterTracking($key)
    {
        $sql = "SELECT * FROM master_tracking WHERE order_id='$key' OR trackingnum='$key'";
        $info = $this->db->fetchOne($sql);
        return $info;
    }

    public function getShippingEasy($key)
    {
        $sql = "SELECT * FROM shippingeasy WHERE OrderNumber='$key' OR TrackingNumber='$key'";
        $info = $this->db->fetchOne($sql);
        return $info;
    }
}
