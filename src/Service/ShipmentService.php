<?php

namespace Service;

use Phalcon\Di\Injectable;

class ShipmentService extends Injectable
{
    public function getMasterTracking($key)
    {
        $sql = "SELECT * FROM master_order_tracking WHERE order_id='$key' OR tracking_number='$key'";
        $info = $this->db->fetchAll($sql);

        if (!$info) {
            $sql = "SELECT * FROM master_order_tracking WHERE order_id LIKE '%$key' ORDER BY ship_date DESC";
            $info = $this->db->fetchAll($sql);
        }

        return $info;
    }

    public function getShippingEasy($key)
    {
        $sql = "SELECT * FROM shippingeasy WHERE OrderNumber='$key' OR TrackingNumber='$key'";
        $info = $this->db->fetchOne($sql);
        return $info;
    }

    public function getOrderTracking($orderId)
    {
        $sql = "SELECT * FROM master_order_tracking WHERE order_id='$orderId'";

        $info = $this->db->fetchOne($sql);

        if ($info) {
            return [
                'orderId'        => $info['order_id'],
                'shipDate'       => $info['ship_date'],
                'carrierCode'    => $info['carrier_code'],
                'carrierName'    => $info['carrier_name'],
                'trackingNumber' => $info['tracking_number'],
                'shipMethod'     => $info['ship_method'],
            ];
        }

        return $info;
    }

    public function addShipment($trackingNum, $site = '')
    {
        $sql = "SELECT * FROM master_order_tracking WHERE tracking_number='$trackingNum'";
        $info = $this->db->fetchOne($sql);

        if (!$info) {
            $info['order_id']        = '';
            $info['carrier_code']    = '';
            $info['tracking_number'] = $trackingNum;
        }

        $info['site'] = $site;

        try {
            $sql = "SELECT * FROM master_shipment WHERE tracking_number='$trackingNum'";
            $found = $this->db->fetchOne($sql);

            if ($found) {
                $this->db->updateAsDict('master_shipment',
                    [
                        'order_id' => $info['order_id'],
                        'carrier'  => $info['carrier_code'],
                        'site'     => $info['site'],
                    ],
                    "tracking_number='$trackingNum'"
                );
            } else {
                $this->db->insertAsDict('master_shipment', [
                    'order_id'        => $info['order_id'],
                    'carrier'         => $info['carrier_code'],
                    'tracking_number' => $info['tracking_number'],
                    'site'            => $info['site'],
                ]);
            }
        } catch (\Exception $e) {
            //echo $e->getMessage();
        }

        return $info;
    }

    /**
     * Order SHIPPED means the tracking number of the order has been uploaded
     */
    public function isOrderShipped($orderId)
    {
        $sql = "SELECT * FROM master_order_shipped WHERE order_id='$orderId'";
        $result = $this->db->fetchOne($sql);
        return $result;
    }

    /**
     * Order SHIPPED means the tracking number of the order has been uploaded.
     * This is used to avoid uploading tracking number twice.
     */
    public function markOrderAsShipped($orderId)
    {
        try {
            $this->db->insertAsDict('master_order_shipped', [
                "order_id" => $orderId,
            ]);
        } catch (\Exception $e) {
            // echo $e->getMessage;
        }
    }
}
