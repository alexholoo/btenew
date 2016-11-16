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
        $address = $this->order->shippingAddress->address;
        $city    = $this->order->shippingAddress->city;
        $state   = $this->order->shippingAddress->province;
        $zipcode = $this->order->shippingAddress->zipcode;
        $country = $this->order->shippingAddress->country;
        $contact = $this->order->shippingAddress->contact;
        $phone   = $this->order->shippingAddress->phone;
        $email   = $this->order->shippingAddress->email;

        $notifyEmail = $this->order->notifyEmail;

        $state = CanadaProvince::nameToCode($state);
        $phone = Utils::formatPhoneNumber($phone, '.');

        $arr = explode("\n", wordwrap($address, 25, "\n"));
        $addr1 = $arr[0];
        $addr2 = isset($arr[1]) ? $arr[1] : '';
        $addr3 = isset($arr[2]) ? $arr[2] : $phone;

        $arr = explode("\n", wordwrap($address, 35, "\n"));
        $euiAddr1 = $arr[0];
        $euiAddr2 = isset($arr[1]) ? $arr[1] : '';
        $euiAddr3 = isset($arr[2]) ? $arr[2] : '';

        $lines[] = "<Header>";
        $lines[] =   "<UserName>$username</UserName>";
        $lines[] =   "<Password>$password</Password>";
        $lines[] =   "<ResponseVersion>1.6</ResponseVersion>";
        $lines[] =   "<OrderTypeCode>DS</OrderTypeCode>";
        $lines[] =   "<PONbr>$orderId</PONbr>";
        $lines[] =   "<RefNbrs>";
        $lines[] =     "<RefIDQual>EU</RefIDQual>";
        $lines[] =     "<RefID>$orderId</RefID>";
        $lines[] =   "</RefNbrs>";
        $lines[] =   "<SalesRequirementCode/>";
        #lines[] =   "<RequestOrderConfirmation>Y</RequestOrderConfirmation>";
        $lines[] =   "<Name>$contact</Name>";
        $lines[] =   "<AddrInfo>";
        $lines[] =     "<Addr>$addr1</Addr>";
        $lines[] =     "<Addr>$addr2</Addr>";
        $lines[] =     "<Addr>$addr3</Addr>";
        $lines[] =   "</AddrInfo>";
        $lines[] =   "<CityName>$city</CityName>";
        $lines[] =   "<StateProvinceCode>$state</StateProvinceCode>";
        $lines[] =   "<PostalCode>$zipcode</PostalCode>";
        $lines[] =   "<ContactName>Roy Zhang</ContactName>";
        $lines[] =   "<ContactPhoneNbr>$phone</ContactPhoneNbr>";
        #lines[] =   "<EndUserCustomerNbr>102555</EndUserCustomerNbr>";
        #lines[] =   "<EndUserGroupCode>ABCDEFGHIJ</EndUserGroupCode>";
        $lines[] =   "<EndUserInfo>";
        $lines[] =     "<EuiGovAgency></EuiGovAgency>";
        $lines[] =     "<EuiGovCabinetLevel></EuiGovCabinetLevel>";
        $lines[] =     "<EuiContractNbr></EuiContractNbr>";
        $lines[] =     "<EuiContractType></EuiContractType>";
        $lines[] =     "<EuiOrderPriority></EuiOrderPriority>";
        $lines[] =     "<EuiMarketType></EuiMarketType>";
        $lines[] =     "<EuiContactName>$contact</EuiContactName>";
        $lines[] =     "<EuiPhoneNbr>$phone</EuiPhoneNbr>";
        $lines[] =     "<EuiFaxNbr></EuiFaxNbr>";
        $lines[] =     "<EuiName>$contact</EuiName>";
        $lines[] =     "<EuiAddr1>$euiAddr1</EuiAddr1>";
        $lines[] =     "<EuiAddr2>$euiAddr2</EuiAddr2>";
        $lines[] =     "<EuiAddr3>$euiAddr3</EuiAddr3>";
        $lines[] =     "<EuiCityName>$city</EuiCityName>";
        $lines[] =     "<EuiStateProvinceCode>$state</EuiStateProvinceCode>";
        $lines[] =     "<EuiPostalCode>$zipcode</EuiPostalCode>";
        $lines[] =     "<EuiCountryCode>$country</EuiCountryCode>";
        $lines[] =     "<EuiSicCode></EuiSicCode>";
        $lines[] =     "<EuiOrderPromoType></EuiOrderPromoType>";
        $lines[] =     "<EuiEndUserLicenseNbr></EuiEndUserLicenseNbr>";
        $lines[] =     "<EuiEndUserPODate></EuiEndUserPODate>";
        $lines[] =     "<EuiEndUserRef1></EuiEndUserRef1>";
        $lines[] =     "<EuiEndUserRef2></EuiEndUserRef2>";
        $lines[] =     "<EuiEndUserRef3></EuiEndUserRef3>";
        $lines[] =     "<EuiInstallName></EuiInstallName>";
        $lines[] =     "<EuiDropShipType></EuiDropShipType>";
        $lines[] =     "<EuiContactEmailAddr1></EuiContactEmailAddr1>";
        $lines[] =     "<EuiContactEmailAddr2></EuiContactEmailAddr2>";
        $lines[] =   "</EndUserInfo>";
        $lines[] =   "<MyOrderTracker>";
        $lines[] =     "<ResellerEmail>$notifyEmail</ResellerEmail>";
        $lines[] =     "<ResellerEvents>OC</ResellerEvents>";
        $lines[] =     "<ResellerEvents>OS</ResellerEvents>";
        $lines[] =   "</MyOrderTracker>";
        #lines[] =   "<OrderLevel>FE</OrderLevel>";
        $lines[] = "</Header>";

        return implode("\n", $lines);
    }

    public function detail()
    {
        $lines = array();

        $branch = $this->order->branch;
        $comment = $this->order->comment;

        $lines[] = "<Detail>";

        foreach ($this->order->items as $item) {
            $sku = $item->sku;
            $qty = $item->qty;

            if (substr($sku, 0, 3) == 'TD-') {
                $sku = substr($sku, 3);
            }

            $lines[] = "<LineInfo>";
            $lines[] =   "<QtyOrdered>$qty</QtyOrdered>";
            $lines[] =   "<ProductIDQual>VP</ProductIDQual>"; // VP - sku is Tech Data item number
            $lines[] =   "<ProductID>$sku</ProductID>";
            $lines[] =   "<WhseCode>$branch</WhseCode>"; // Optional - Tech Data warehouse
            $lines[] =   "<IDCode>UP</IDCode>"; // TODO: Ship via code
            $lines[] =   "<OrderMessageLine>$comment</OrderMessageLine>"; // ??
            $lines[] = "</LineInfo>";
        }

        $lines[] = "</Detail>";

        return implode("\n", $lines);
    }
}
