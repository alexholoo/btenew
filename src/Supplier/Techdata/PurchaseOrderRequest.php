<?php

namespace Supplier\Techdata;

use Toolkit\Utils;
use Toolkit\CanadaProvince;
use Supplier\Model\PurchaseOrderRequest as BaseRequest;

class PurchaseOrderRequest extends BaseRequest
{
    /**
     * @return string
     */
    public function toXml()
    {
        $lines = array();

        $lines[] = "<XML_Order_Submit>";
        $lines[] = $this->header();
        $lines[] = $this->detail();
        $lines[] = "</XML_Order_Submit>";

        return Utils::formatXml(implode("\n", $lines));
    }

    public function header()
    {
        $lines = array();

        $username = $this->config['username'];
        $password = $this->config['password'];

        $orderId = $this->order->orderId;
        $address = $this->order->address;
        $city    = $this->order->city;
        $state   = $this->order->province;
        $zipcode = $this->order->zipcode;
        $country = $this->order->country;
        $contact = $this->order->contact;
        $phone   = $this->order->phone;
        $email   = $this->order->email;

        $state = CanadaProvince::nameToCode($state);

        $lines[] = "<Header>";
        $lines[] =   "<UserName>$username</UserName>";
        $lines[] =   "<Password>$password</Password>";
        $lines[] =   "<ResponseVersion>1.6</ResponseVersion>";
        $lines[] =   "<OrderTypeCode>DS</OrderTypeCode>";
        $lines[] =   "<PONbr>$orderId</PONbr>";
        $lines[] =   "<SalesRequirementCode/>";
        #lines[] =   "<RequestOrderConfirmation>Y</RequestOrderConfirmation>";
        $lines[] =   "<Name>$contact</Name>";
        $lines[] =   "<AddrInfo>";
        $lines[] =     "<Addr>$address</Addr>";
        $lines[] =   "</AddrInfo>";
        $lines[] =   "<CityName>$city</CityName>";
        $lines[] =   "<StateProvinceCode>$state</StateProvinceCode>";
        $lines[] =   "<PostalCode>$zipcode</PostalCode>";
        $lines[] =   "<ContactName>$contact</ContactName>";
        $lines[] =   "<ContactPhoneNbr>$phone</ContactPhoneNbr>";
        #lines[] =   "<OrderLevel>FE</OrderLevel>";
        $lines[] = "</Header>";

        return implode("\n", $lines);
    }

    public function detail()
    {
        $lines = array();

        $sku = $this->order->sku;
        $qty = $this->order->qty;
        $branch = $this->order->branch;
        $comment = $this->order->comment;

        if (substr($sku, 0, 3) == 'TD-') {
            $sku = substr($sku, 3);
        }

        $lines[] = "<Detail>";
        $lines[] =   "<LineInfo>";
        $lines[] =     "<QtyOrdered>$qty</QtyOrdered>";
        $lines[] =     "<ProductIDQual>VP</ProductIDQual>"; // VP - sku is Tech Data item number
        $lines[] =     "<ProductID>$sku</ProductID>";
        $lines[] =     "<WhseCode>$branch</WhseCode>"; // Optional - Tech Data warehouse
        $lines[] =     "<IDCode>UP</IDCode>"; // TODO: Ship via code
        $lines[] =     "<OrderMessageLine>$comment</OrderMessageLine>"; // ??
        $lines[] =   "</LineInfo>";
        $lines[] = "</Detail>";

        return implode("\n", $lines);
    }
}
