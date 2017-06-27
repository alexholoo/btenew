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

        // check if the order shipped by BTE (tracking number scanned)
        foreach ($info as $key => $row) {
            $info[$key]['shipped'] = false;

            $trackingNum = $row['tracking_number'];
            $sql = "SELECT * FROM master_shipment WHERE tracking_number='$trackingNum'";
            $found = $this->db->fetchOne($sql);

            if ($found) {
                $info[$key]['shipped'] = true;
            }
        }

        return $info;
    }

    public function getShippingEasy($key)
    {
        $sql = "SELECT * FROM shippingeasy WHERE OrderNumber='$key' OR TrackingNumber='$key'";
        $info = $this->db->fetchOne($sql);
        return $info;
    }

    public function getOrderByTracking($trackingNum)
    {
        $sql = "SELECT * FROM master_order_tracking WHERE tracking_number='$trackingNum'";
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

    public function addShipment($trackingNum, $user)
    {
        $sql = "SELECT * FROM master_order_tracking WHERE tracking_number='$trackingNum'";
        $info = $this->db->fetchOne($sql);

        if (!$info) {
            $info['order_id']        = '';
            $info['carrier_code']    = '';
            $info['tracking_number'] = $trackingNum;
        }

        $info['user'] = $user;

        try {
            $sql = "SELECT * FROM master_shipment WHERE tracking_number='$trackingNum'";
            $found = $this->db->fetchOne($sql);

            if ($found) {
                $this->db->updateAsDict('master_shipment',
                    [
                        'order_id' => $info['order_id'],
                        'carrier'  => $info['carrier_code'],
                        'user'     => $info['user'],
                    ],
                    "tracking_number='$trackingNum'"
                );
            } else {
                $this->db->insertAsDict('master_shipment', [
                    'order_id'        => $info['order_id'],
                    'carrier'         => $info['carrier_code'],
                    'tracking_number' => $info['tracking_number'],
                    'user'            => $info['user'],
                ]);
            }
        } catch (\Exception $e) {
            //echo $e->getMessage();
        }

        return $info;
    }

    public function getShipmentReport($date = '')
    {
        $where = '';

        if ($date) {
            $where = "WHERE date(createdon)='$date'";
        }

        $sql = "SELECT * FROM master_shipment $where ORDER BY createdon";
        $rows = $this->db->fetchAll($sql);

        return $rows;
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

    public function getRates($orderId)
    {
        $order = $this->orderService->getOrder($orderId);
        if (!$order) {
            return false;
        }

        $order['items']   = $this->orderService->getOrderItems($orderId);
        $order['address'] = $this->orderService->getShippingAddress($orderId);

        $rates = [];
        $rates['fedex'] = $this->fedexService->getRate($order);
        $rates['ups']   = $this->upsService->getRate($order);

        return $rates;
    }
}
