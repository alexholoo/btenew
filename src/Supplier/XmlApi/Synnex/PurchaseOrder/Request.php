<?php

namespace Supplier\XmlApi\Synnex\PurchaseOrder;

class Request
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $order;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function addOrder($order)
    {
        $this->order = $order;
    }

    public function toXml()
    {
        $lines = array();
        $lines[] = '<?xml version="1.0" encoding="UTF-8" ?>';
        $lines[] = '<SynnexB2B>';
        $lines[] = $this->credential();
        $lines[] = $this->orderRequest();
        $lines[] = '</SynnexB2B>';

        return implode("\n", $lines);
    }

    protected function credential()
    {
        $username = $this->config['username'];
        $password = $this->config['password'];

        $lines = array();
        $lines[] = '<Credential>';
        $lines[] =   "<UserID>$username</UserID>";
        $lines[] =   "<Password>$password</Password>";
        $lines[] = '</Credential>';

        return implode("\n", $lines);
    }

    protected function orderRequest()
    {
        $customerNo = $this->config['customerNo'];
        $poNumber   = $this->order['orderNo'];

        $lines = array();
        $lines[] = '<OrderRequest>';
        $lines[] =   "<CustomerNumber>$customerNo</CustomerNumber>";
        $lines[] =   "<PONumber>$poNumber</PONumber>";
        $lines[] =   '<DropShipFlag>Y</DropShipFlag>';

        $lines[] = $this->shipment();
        $lines[] = $this->payment();

        if (($endUserPoNumber = $this->order['endUserPoNumber'])) {
            $lines[] = "<EndUserPONumber>$endUserPoNumber</EndUserPONumber>";
        }

        if (($comment = $this->order['comment'])) {
            $lines[] = "<Comment>$comment</Comment>";
        }

        $lines[] = $this->items();

        $lines[] = '</OrderRequest>';

        return implode("\n", $lines);
    }

    protected function shipment()
    {
        #warehouse  = $this->order['warehouse'];
        $address    = $this->order['address'];
        $city       = $this->order['city'];
        $state      = $this->order['state'];
        $zipcode    = $this->order['zipcode'];
        $country    = $this->order['country'];
        $contact    = $this->order['contact'];
        $phone      = $this->order['phone'];
        $email      = $this->order['email'];
        #shipMethod = $this->order['shipMethod'];

        $lines = array();
        $lines[] = '<Shipment>';
        #lines[] =   "<ShipFromWarehouse>$warehouse</ShipFromWarehouse>";
        $lines[] =   '<ShipTo>';
        #lines[] =     "<AddressName1>Manners Industry, Inc.</AddressName1>";
        #lines[] =     "<AddressName2 />";
        $lines[] =     "<AddressLine1>$address</AddressLine1>";
        $lines[] =     "<City>$city</City>";
        $lines[] =     "<State>$state</State>";
        $lines[] =     "<ZipCode>$zipcode</ZipCode>";
        $lines[] =     "<Country>$country</Country>";
        $lines[] =   '</ShipTo>';
        $lines[] =   '<ShipToContact>';
        $lines[] =     "<ContactName>$contact</ContactName>";
        $lines[] =     "<PhoneNumber>$phone</PhoneNumber>";
        $lines[] =     "<EmailAddress>$email</EmailAddress>";
        $lines[] =   '</ShipToContact>';
        #lines[] =   '<ShipMethod>';
        #lines[] =     "<Code>$shipMethod</Code>";
        #lines[] =   '</ShipMethod>';
        $lines[] = '</Shipment>';

        return implode("\n", $lines);
    }

    protected function payment()
    {
        $accountNo = $this->config['accountNo'];

        $lines = array();
        $lines[] = '<Payment>';
        $lines[] =   '<BillTo code="'. $accountNo. '"></BillTo>';
        $lines[] = '</Payment>';

        return implode("\n", $lines);
    }

    protected function items()
    {
        $sku   = $this->order['sku'];
        $price = $this->order['price'];
        $qty   = $this->order['qty'];

        $lines = array();
        $lines[] = '<Items>';
        $lines[] =   '<Item lineNumber="1">';
        $lines[] =     "<SKU>$sku</SKU>";
        $lines[] =     "<UnitPrice>$price</UnitPrice>";
        $lines[] =     "<OrderQuantity>$qty</OrderQuantity>";
        $lines[] =   '</Item>';
        $lines[] = '</Items>';

        return implode("\n", $lines);
    }
}
