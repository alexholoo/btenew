<?php

namespace Supplier\DH;

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

        $lines[] = '<XMLFORMPOST>';
        $lines[] = '<REQUEST>orderEntry</REQUEST>';
        $lines[] = $this->login();
        $lines[] = $this->orderHeader();
        $lines[] = $this->orderItems();
        $lines[] = '</XMLFORMPOST>';

        $xmldoc = Utils::formatXml(implode("\n", $lines));

        return $xmldoc;

       #return "xmlDoc=" . $xmldoc; // Post an HTML form
       #return "xmlDoc=" . rawurlencode($xmldoc); // More safer way to post an HTML form
    }

    public function login()
    {
        $lines = array();

        $userid = $this->config['username'];
        $passwd = $this->config['password'];

        $lines[] = "<LOGIN>";
        $lines[] =   "<USERID>$userid</USERID>";
        $lines[] =   "<PASSWORD>$passwd</PASSWORD>";
        $lines[] = "</LOGIN>";

        return implode("\n", $lines);
    }

    public function orderHeader()
    {
        $lines = array();

        $dropShipPassword = $this->config['dropshippw'];

        $orderId = $this->order->orderId;
        $contact = $this->order->contact;
        $address = $this->order->address;
        $city    = $this->order->city;
        $state   = $this->order->province;
        $zipcode = $this->order->zipcode;
        $phone   = $this->order->phone;
        $country = $this->order->country;
        $comment = $this->order->comment;

        $state = CanadaProvince::nameToCode($state);

        $arr = explode("\n", wordwrap($address, 30, "\n"));
        $addr1 = $arr[0];
        $addr2 = isset($arr[1]) ? $arr[1] . " $phone" : $phone;

        $partShip    = $this->config['partship'];
        $backOrder   = $this->config['backorder'];
        $shipCarrier = $this->config['shipcarrier'];
        $shipService = $this->config['shipservice'];
        $onlyBranch  = $this->config['onlybranch'];
        $branches    = $this->config['branches'];

        $lines[] = "<ORDERHEADER>";
        $lines[] =   "<ONLYBRANCH>$onlyBranch</ONLYBRANCH>";
        $lines[] =   "<BRANCHES>$branches</BRANCHES>";
        $lines[] =   "<PARTSHIPALLOW>$partShip</PARTSHIPALLOW>";
        $lines[] =   "<BACKORDERALLOW>$backOrder</BACKORDERALLOW>";
        $lines[] =   "<DROPSHIPPW>$dropShipPassword</DROPSHIPPW>";
        $lines[] =   "<SHIPTONAME>$contact</SHIPTONAME>";
        $lines[] =   "<SHIPTOATTN></SHIPTOATTN>";
        $lines[] =   "<SHIPTOADDRESS>$addr1</SHIPTOADDRESS>";
        $lines[] =   "<SHIPTOADDRESS2>$addr2</SHIPTOADDRESS2>";
        $lines[] =   "<SHIPTOCITY>$city</SHIPTOCITY>";
        $lines[] =   "<SHIPTOPROVINCE>$state</SHIPTOPROVINCE>";
        $lines[] =   "<SHIPTOPOSTALCODE>$zipcode</SHIPTOPOSTALCODE>";
        $lines[] =   "<SHIPCARRIER>$shipCarrier</SHIPCARRIER>";
        $lines[] =   "<SHIPSERVICE>$shipService</SHIPSERVICE>";
        $lines[] =   "<PONUM>$orderId</PONUM>";
        $lines[] =   "<REMARKS>$comment</REMARKS>";
        $lines[] = "</ORDERHEADER>";

        return implode("\n", $lines);
    }

    public function orderItems()
    {
        $lines = array();

        $sku = $this->order->sku;
        $qty = $this->order->qty;
        $branch = $this->order->branch;

        if (substr($sku, 0, 3) == 'DH-') {
            $sku = substr($sku, 3);
        }

        $lines[] = "<ORDERITEMS>";
        $lines[] =   "<ITEM>";
        $lines[] =     "<PARTNUM>$sku</PARTNUM>";
        $lines[] =     "<QTY>$qty</QTY>";
        if ($branch) {
            $lines[] = "<BRANCH>$branch</BRANCH>";
        }
        $lines[] =   "</ITEM>";
        $lines[] = "</ORDERITEMS>";

        return implode("\n", $lines);
    }
}
