<?php

namespace Supplier\DH;

use Toolkit\Utils;
use Supplier\Model\OrderStatusRequest as BaseRequest;

class OrderStatusRequest extends BaseRequest
{
    /**
     * @return string
     */
    public function toXml()
    {
        $username = $this->config['username'];
        $password = $this->config['password'];
        $orderId  = $this->orderId;

        $lines = array();

        $lines[] = "<XMLFORMPOST>";
        $lines[] = "<REQUEST>orderStatus</REQUEST>";
        $lines[] = "<LOGIN>";
        $lines[] =   "<USERID>$username</USERID>";
        $lines[] =   "<PASSWORD>$password</PASSWORD>";
        $lines[] = "</LOGIN>";
        $lines[] = "<STATUSREQUEST>";
        $lines[] =   "<PONUM>$orderId</PONUM>";
        $lines[] = "</STATUSREQUEST>";
        #lines[] = "<STATUSREQUEST>";
        #lines[] =   "<ORDERNUM>DandHordernumber</ORDERNUM>";
        #lines[] = "</STATUSREQUEST>";
        #lines[] = "<STATUSREQUEST>";
        #lines[] =   "<INVOICE>DandHinvoicenumber</INVOICE>";
        #lines[] = "</STATUSREQUEST>";
        $lines[] = "</XMLFORMPOST>";

        $xmldoc = Utils::formatXml(implode("\n", $lines));

        return $xmldoc;
       #return "xmlDoc=" . $xmldoc;
    }
}
