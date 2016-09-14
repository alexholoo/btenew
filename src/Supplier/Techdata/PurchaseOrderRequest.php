<?php

namespace Supplier\Techdata;

use Utility\Utils;
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

        $userid = $this->config['username'];
        $passwd = $this->config['password'];

        $orderId = $this->order->orderId;
        $address = $this->order->address;
        $city    = $this->order->city;
        $state   = $this->order->province;
        $zipcode = $this->order->zipcode;
        $country = $this->order->country;
        $contact = $this->order->contact;
        $phone   = $this->order->phone;
        $email   = $this->order->email;

        $lines[] = "<Header>";
        $lines[] =   "<UserName>$userid</UserName>";
        $lines[] =   "<Password>$passwd</Password>";
        $lines[] =   "<ResponseVersion>1.6</ResponseVersion>";
        $lines[] =   "<PONbr>$orderId</PONbr>";
        $lines[] =   "<EndUserInfo>";
        $lines[] =     "<EuiContactName>$contact</EuiContactName>";
        $lines[] =     "<EuiPhoneNbr>$phone</EuiPhoneNbr>";
        $lines[] =     "<EuiName>$contact</EuiName>";
        $lines[] =     "<EuiAddr1>$address</EuiAddr1>";
        $lines[] =     "<EuiAddr2></EuiAddr2>";
        $lines[] =     "<EuiAddr3></EuiAddr3>";
        $lines[] =     "<EuiCityName>$city</EuiCityName>";
        $lines[] =     "<EuiStateProvinceCode>$state</EuiStateProvinceCode>";
        $lines[] =     "<EuiPostalCode>$zipcode</EuiPostalCode>";
        $lines[] =     "<EuiCountryCode>$country</EuiCountryCode>";
        $lines[] =     "<EuiDropShipType>D</EuiDropShipType>"; // Reserved for future use
        $lines[] =     "<EuiContactEmailAddr1>$email</EuiContactEmailAddr1>";
        $lines[] =   "</EndUserInfo>";
        $lines[] =   "<MyOrderTracker>";
        $lines[] =     "<EndUserEmail>$email</EndUserEmail>";
        $lines[] =   "</MyOrderTracker>";
        $lines[] = "</Header>";

        return implode("\n", $lines);
    }

    public function detail()
    {
        $lines = array();

        $sku = $this->order->sku;
        $qty = $this->order->qty;
        $branch = $this->order->branch;
        $comment = htmlspecialchars($this->order->comment);

        if (substr($sku, 0, 3) == 'TD-') {
            $sku = substr($sku, 3);
        }

        $lines[] = "<Detail>";
        $lines[] =   "<LineInfo>";
        $lines[] =     "<QtyOrdered>$qty</QtyOrdered>";
        $lines[] =     "<ProductIDQual>VP</ProductIDQual>"; // VP - sku is Tech Data item number
        $lines[] =     "<ProductID>$sku</ProductID>";
        $lines[] =     "<WhseCode>$branch</WhseCode>"; // Optional - Tech Data warehouse
        $lines[] =     "<IDCode>01</IDCode>"; // TODO: Ship via code
        $lines[] =     "<OrderMessageLine>$comment</OrderMessageLine>"; // ??
        $lines[] =   "</LineInfo>";
        $lines[] = "</Detail>";

        return implode("\n", $lines);
    }

    /**
     * @return boolean
     */
    protected function initConfig()
    {
        if (!$this->config) {
            $this->config = $this->client->getXmlApiConfig(\Supplier\ConfigKey::TECHDATA);
            return true;
        }
        return false;
    }
}
