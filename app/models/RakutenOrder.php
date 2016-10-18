<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class RakutenOrder extends Model
{
    public $id;
    public $sellerShopperNumber;
    public $receiptID;
    public $receiptItemID;
    public $listingID;
    public $dateEntered;
    public $sku;
    public $referenceId;
    public $quantity;
    public $qtyShipped;
    public $qtyCancelled;
    public $title;
    public $price;
    public $productRev;
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
    public $shipToName;
    public $shipToCompany;
    public $shipToStreet1;
    public $shipToStreet2;
    public $shipToCity;
    public $shipToState;
    public $shipToZip;
    public $shippingMethodId;

    public function getSource()
    {
        return 'rakuten_order_report';
    }

    public function initialize()
    {
    }

    public function columnMap()
    {
        // Keys are the real names in the table and
        // the values their names in the application

        return array(
            'id'                  => 'id',
            'SellerShopperNumber' => 'sellerShopperNumber',
            'Receipt_ID'          => 'receiptID',
            'Receipt_Item_ID'     => 'receiptItemID',
            'ListingID'           => 'listingID',
            'Date_Entered'        => 'dateEntered',
            'Sku'                 => 'sku',
            'ReferenceId'         => 'referenceId',
            'Quantity'            => 'quantity',
            'Qty_Shipped'         => 'qtyShipped',
            'Qty_Cancelled'       => 'qtyCancelled',
            'Title'               => 'title',
            'Price'               => 'price',
            'Product_Rev'         => 'productRev',
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
            'Ship_To_Name'        => 'shipToName',
            'Ship_To_Company'     => 'shipToCompany',
            'Ship_To_Street1'     => 'shipToStreet1',
            'Ship_To_Street2'     => 'shipToStreet2',
            'Ship_To_City'        => 'shipToCity',
            'Ship_To_State'       => 'shipToState',
            'Ship_To_Zip'         => 'shipToZip',
            'ShippingMethodId'    => 'shippingMethodId',
        );
    }
}
