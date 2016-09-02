<?php

namespace Supplier\DH;

use Supplier\Model\PurchaseOrderRequest as BaseRequest;

class PurchaseOrderRequest extends Request
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

        return Utils::formatXml(implode("\n", $lines));
    }

    public function login()
    {
        $lines = array();

        $userid = $this->config['username'];
        $passwd = $this->config['password'];

        $lines[] = "<LOGIN>";
        $lines[] = "  <USERID>$userid</USERID>";
        $lines[] = "  <PASSWORD>$passwd</PASSWORD>";
        $lines[] = "</LOGIN>";

        return implode("\n", $lines);
    }

    public function orderHeader()
    {
        $lines = array();

        $dropShipPassword = $this->config['dropship'];

        $orderNo = $this->order['orderId'];
        $contact = $this->order['buyer'];
        $address = $this->order['address'];
        $city = $this->order['city'];
        $state = $this->order['province'];
        $postalcode = $this->order['postalcode'];
        $country = $this->order['country'];
        $comment = $this->order['comment'];

        $lines[] = "<ORDERHEADER>";
        $lines[] =   "<ONLYBRANCH></ONLYBRANCH>";
        $lines[] =   "<BRANCHES>3</BRANCHES>";
        $lines[] =   "<PARTSHIPALLOW>N</PARTSHIPALLOW>";
        $lines[] =   "<BACKORDERALLOW>N</BACKORDERALLOW>";
        $lines[] =   "<DROPSHIPPW>$dropShipPassword</DROPSHIPPW>";
        $lines[] =   "<SHIPTONAME>$contact</SHIPTONAME>";
        $lines[] =   "<SHIPTOATTN></SHIPTOATTN>";
        $lines[] =   "<SHIPTOADDRESS>$address</SHIPTOADDRESS>";
        $lines[] =   "<SHIPTOADDRESS2></SHIPTOADDRESS2>";
        $lines[] =   "<SHIPTOCITY>$city</SHIPTOCITY>";
        $lines[] =   "<SHIPTOPROVINCE>$state</SHIPTOPROVINCE>";
        $lines[] =   "<SHIPTOPOSTALCODE>$postalcode</SHIPTOPOSTALCODE>";
        $lines[] =   "<SHIPCARRIER>Purolator</SHIPCARRIER>";
        $lines[] =   "<SHIPSERVICE>Ground</SHIPSERVICE>";
        $lines[] =   "<PONUM>$orderNo</PONUM>";
        $lines[] =   "<REMARKS>$comment</REMARKS>";
        $lines[] = "</ORDERHEADER>";

        return implode("\n", $lines);
    }

    public function orderItems()
    {
        $lines = array();

        $sku = $this->order['sku'];
        $qty = $this->order['qty'];
        $branch = $this->order['branch'];

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
