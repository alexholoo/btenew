<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class rakuten_order_shipping_address extends Model
{
    public $id;
    public $receiptID;
    public $shipToName;
    public $shipToCompany;
    public $shipToStreet1;
    public $shipToStreet2;
    public $shipToCity;
    public $shipToState;
    public $shipToZip;

    public function getSource()
    {
        return 'rakuten_order_shipping_address';
    }

    public function initialize()
    {
    }

    public function columnMap()
    {
        // Keys are the real names in the table and
        // the values their names in the application

        return array(
            'id'              => 'id',
            'Receipt_ID'      => 'receiptID',
            'Ship_To_Name'    => 'shipToName',
            'Ship_To_Company' => 'shipToCompany',
            'Ship_To_Street1' => 'shipToStreet1',
            'Ship_To_Street2' => 'shipToStreet2',
            'Ship_To_City'    => 'shipToCity',
            'Ship_To_State'   => 'shipToState',
            'Ship_To_Zip'     => 'shipToZip',
        );
    }
}
