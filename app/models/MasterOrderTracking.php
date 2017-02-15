<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class MasterOrderTracking extends Model
{
    public $orderId;
    public $shipDate;
    public $carrier;
    public $shipMethod;
    public $trackingNumber;
    public $sender;

    public function getSource()
    {
        return 'master_order_tracking';
    }

    public function initialize()
    {
        # $this->hasOne("this_id", "That\\Model", "that_id");
        # $this->hasMany("this_id", "That\\Model", "that_id");
        # $this->belongsTo("this_id", "That\\Model", "that_id");
        # $this->hasManyToMany(...);
    }

    public function columnMap()
    {
        // Keys are the real names in the table and
        // the values their names in the application

        return array(
            'order_id'        => 'orderId',
            'ship_date'       => 'shipDate',
            'carrier'         => 'carrier',
            'ship_method'     => 'shipMethod',
            'tracking_number' => 'trackingNumber',
            'sender'          => 'sender',
        );
    }
}
