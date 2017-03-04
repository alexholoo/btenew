<?php

abstract class TrackingImporter extends Job
{
    abstract public function import();

    protected function saveToDb($fields)
    {
        try {
            $this->db->insertAsDict('master_order_tracking', [
                'order_id'        => trim($fields['orderId']),
                'ship_date'       => $fields['shipDate'],
                'carrier_code'    => $fields['carrierCode'],
                'carrier_name'    => $fields['carrierName'],
                'ship_method'     => $fields['shipMethod'],
                'tracking_number' => $fields['trackingNumber'],
                'sender'          => $fields['sender'],
            ]);
        } catch (Exception $e) {
            // echo $e->getMessage(), EOL;
        }
    }
}
