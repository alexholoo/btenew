<?php

class TrackingImporter extends Job
{
    public function import() { }

    protected function saveToDb($fields)
    {
        try {
            $this->db->insertAsDict('master_order_tracking', [
                'order_id'        => $fields['orderId'],
                'ship_date'       => $fields['shipDate'],
                'carrier'         => $fields['carrier'],
                'ship_method'     => $fields['shipMethod'],
                'tracking_number' => $fields['trackingNumber'],
                'sender'          => $fields['sender'],
            ]);
        } catch (Exception $e) {
            // echo $e->getMessage(), EOL;
        }
    }
}
