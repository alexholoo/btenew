<?php

namespace Supplier\DH;

use Toolkit\Utils;
use Supplier\Model\BatchPurchaseRequest as BaseRequest;

class BatchPurchaseRequest extends BaseRequest
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

        $orderId = $this->getOrderId();

        $contact = $this->address['name'];
        $address = $this->address['address'];
        $city    = $this->address['city'];
        $state   = $this->address['province'];
        $zipcode = $this->address['zipcode'];
        $phone   = $this->address['phone'];
        $country = $this->address['country'];
        $comment = '';

        $arr = explode("\n", wordwrap($address, 30, "\n"));
        $addr1 = $arr[0];
        $addr2 = isset($arr[1]) ? $arr[1] : '';

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

        $lines[] = "<ORDERITEMS>";

        foreach ($this->orders as $order) {
            $sku = $order['sku'];
            $qty = $order['qty'];

            if (substr($sku, 0, 3) == 'DH-') {
                $sku = substr($sku, 3);
            }

            $lines[] = "<ITEM>";
            $lines[] =   "<PARTNUM>$sku</PARTNUM>";
            $lines[] =   "<QTY>$qty</QTY>";
            $lines[] = "</ITEM>";
        }

        $lines[] = "</ORDERITEMS>";

        return implode("\n", $lines);
    }

    public function getOrderId()
    {
        if (empty($this->orderId)) {
            $this->orderId = 'DH-'.date('Ymd-hi');
        }

        return $this->orderId;
    }
}
