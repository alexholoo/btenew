<?php

namespace Supplier\Techdata;

use Utility\Utils;
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

        // PurposeCode 	The type of order status being requested.
        //
        // Value 	Meaning
        // =================
        // 01 	    PO Status List
        // 02 	    Order/Invoice Detail
        // 03 	    Shipment/Invoice Detail

        $purposeCode = '01';

        $lines = array();
        $lines[] = '<XML_OrderStatus_Submit>';
        $lines[] = '<Header>';
        $lines[] =   "<UserName>$username</UserName>";
        $lines[] =   "<Password>$password</Password>";
        $lines[] =   '<TransSetIDCode></TransSetIDCode>';
        $lines[] =   '<TransControlID></TransControlID>';
        $lines[] =   '<ResponseVersion>1.3</ResponseVersion>';
        $lines[] = '</Header>';
        $lines[] = '<Detail>';
        $lines[] =   "<PurposeCode>$purposeCode</PurposeCode>";
        $lines[] =   '<EDIInd>N</EDIInd>';
        $lines[] =   '<NonEDIInd>Y</NonEDIInd>';
        $lines[] =   '<RefInfo>';
        $lines[] =      '<RefIDQual>PO</RefIDQual>'; // see comment below
        $lines[] =      "<RefID>$orderId</RefID>";
        $lines[] =   '</RefInfo>';
        $lines[] = '</Detail>';
        $lines[] = '<Summary>';
        $lines[] =   '<NbrOfSegments/>';
        $lines[] = '</Summary>';
        $lines[] = '</XML_OrderStatus_Submit>';

        // RefIDQual 	Type of identifier that will be contained in the following <RefID> element.
        //
        // Value   Meaning
        // ================
        // ON 	   Tech Data assigned sales order number (PurposeCode 02 or 03 only)
        // PO 	   Customer assigned purchase Order number (PurposeCode 01 only)
        // IN 	   Tech Data assigned invoice number (PurposeCode 02 or 03 only)

        return Utils::formatXml(implode("\n", $lines));
    }
}
