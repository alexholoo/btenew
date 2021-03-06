<?php

namespace Supplier\Synnex;

use Toolkit\Utils;
use Supplier\Model\PurchaseOrderRequest as BaseRequest;

class PurchaseOrderRequest extends BaseRequest
{
    /**
     * @return string
     */
    public function toXml()
    {
        $lines = array();
        $lines[] = '<?xml version="1.0" encoding="UTF-8" ?>';
        $lines[] = '<SynnexB2B>';
        $lines[] = $this->credential();
        $lines[] = $this->orderRequest();
        $lines[] = '</SynnexB2B>';

        return Utils::formatXml(implode("\n", $lines));
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
        $poNumber   = $this->order->orderId;
        $endUserPO  = $this->order->endUserPO;
        $comment    = $this->order->comment;

        $lines = array();
        $lines[] = '<OrderRequest>';
        $lines[] =   "<CustomerNumber>$customerNo</CustomerNumber>";
        $lines[] =   "<PONumber>$poNumber</PONumber>";
        $lines[] =   '<DropShipFlag>Y</DropShipFlag>';

        $lines[] = $this->shipment();
        #lines[] = $this->payment();

        if ($endUserPO) {
            $lines[] = "<EndUserPONumber>$endUserPO</EndUserPONumber>";
        }

        if ($comment) {
            $lines[] = "<Comment>$comment</Comment>";
        }

        $lines[] = $this->items();

        $lines[] = '</OrderRequest>';

        return implode("\n", $lines);
    }

    protected function shipment()
    {
        $address    = $this->order->address;
        $city       = $this->order->city;
        $state      = $this->order->province;
        $zipcode    = $this->order->zipcode;
        $country    = $this->order->country;
        $contact    = $this->order->contact;
        $phone      = $this->order->phone;
        $email      = $this->order->email;
        $branch     = $this->order->branch;
        $shipMethod = $this->config['shipmethod'];

        $lines = array();
        $lines[] = '<Shipment>';
        $lines[] =   "<ShipFromWarehouse>$branch</ShipFromWarehouse>";
        $lines[] =   '<ShipTo>';
        $lines[] =     "<AddressName1>$contact</AddressName1>";
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
        $lines[] =   '<ShipMethod>';
        $lines[] =     "<Code>$shipMethod</Code>";
        $lines[] =   '</ShipMethod>';
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
        $sku   = $this->order->sku;
        $price = $this->order->price;
        $qty   = $this->order->qty;

        if (substr($sku, 0, 4) == 'SYN-') {
            $sku = substr($sku, 4);
        }

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
