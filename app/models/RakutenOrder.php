<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class RakutenOrder extends Model
{
    public $sellerShopperNumber;
    public $receiptID;
    public $dateEntered;
    public $shippingCost;
    public $productOwed;
    public $shippingOwed;
    public $commission;
    public $shippingFee;
    public $perItemFee;
    public $taxCost;
    public $billToCompany;
    public $billToPhone;
    public $billToFname;
    public $billToLname;
    public $email;
    public $shippingMethodId;

    public function getSource()
    {
        return 'rakuten_order_report';
    }

    public function initialize()
    {
        $this->hasMany('receiptID', 'App\\Models\\RakutenOrderItem',            'receiptID', ['alias' => 'items']);
        $this->hasOne('receiptID',  'App\\Models\\RakutenOrderShippingAddress', 'receiptID', ['alias' => 'shippingAddress']);
    }

    public function columnMap()
    {
        // Keys are the real names in the table and
        // the values their names in the application

        return array(
            'id'                  => 'id',
            'SellerShopperNumber' => 'sellerShopperNumber',
            'Receipt_ID'          => 'receiptID',
            'Date_Entered'        => 'dateEntered',
            'Shipping_Cost'       => 'shippingCost',
            'ProductOwed'         => 'productOwed',
            'ShippingOwed'        => 'shippingOwed',
            'Commission'          => 'commission',
            'ShippingFee'         => 'shippingFee',
            'PerItemFee'          => 'perItemFee',
            'Tax_Cost'            => 'taxCost',
            'Bill_To_Company'     => 'billToCompany',
            'Bill_To_Phone'       => 'billToPhone',
            'Bill_To_Fname'       => 'billToFname',
            'Bill_To_Lname'       => 'billToLname',
            'Email'               => 'email',
            'ShippingMethodId'    => 'shippingMethodId',
        );
    }
}
