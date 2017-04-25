<?php

namespace Supplier\Techdata;

use Toolkit\Utils;
use Supplier\Model\OrderStatusRequest as BaseRequest;

class OrderStatusRequest extends BaseRequest
{
    protected $purposeCode;
    protected $orderNum;
    protected $poNumber;
    protected $invoice;

    public function setPurpose($purposeCode)
    {
        $this->purposeCode = $purposeCode;
    }

    public function setOrder($orderId)
    {
        $this->poNumber = $orderId;
    }

    public function setOrderNum($orderNum)
    {
        $this->orderNum = $orderNum;
    }

    public function setPoNumber($poNumber)
    {
        $this->poNumber = $poNumber;
    }

    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * @return string
     */
    public function build()
    {
        $username = $this->config['username'];
        $password = $this->config['password'];

        // PurposeCode 	The type of order status being requested.
        //
        // Value 	Meaning
        // =================
        // 01 	    PO Status List
        // 02 	    Order/Invoice Detail
        // 03 	    Shipment/Invoice Detail

        $purposeCode = $this->purposeCode;

        $lines = array();
        $lines[] = '<XML_OrderStatus_Submit>';
        $lines[] = '<Header>';
        $lines[] =   "<UserName>$username</UserName>";
        $lines[] =   "<Password>$password</Password>";
        $lines[] =   '<TransSetIDCode>869</TransSetIDCode>';
        $lines[] =   '<TransControlID>10000</TransControlID>';
        $lines[] =   '<ResponseVersion>1.3</ResponseVersion>';
        $lines[] = '</Header>';
        $lines[] = '<Detail>';
        $lines[] =   "<PurposeCode>$purposeCode</PurposeCode>";
        $lines[] =   '<EDIInd>N</EDIInd>';
        $lines[] =   '<NonEDIInd>Y</NonEDIInd>';
        $lines[] =   $this->RefInfo();
        $lines[] = '</Detail>';
        $lines[] = '<Summary>';
        $lines[] =   '<NbrOfSegments/>';
        $lines[] = '</Summary>';
        $lines[] = '</XML_OrderStatus_Submit>';

        return Utils::formatXml(implode("\n", $lines));
    }

    protected function refInfo()
    {
        // RefIDQual 	Type of identifier that will be contained in the following <RefID> element.
        //
        // Value   Meaning
        // ================
        // ON 	   Tech Data assigned sales order number (PurposeCode 02 or 03 only)
        // PO 	   Customer assigned purchase Order number (PurposeCode 01 only)
        // IN 	   Tech Data assigned invoice number (PurposeCode 02 or 03 only)

        $poNumber = $this->poNumber;
        $invoice  = $this->invoice;
        $orderNum = $this->orderNum;

        $purposeCode = $this->purposeCode;

        $lines = array();

        if ($purposeCode == '01') {
            $lines[] = '<RefInfo>';
            $lines[] =    '<RefIDQual>PO</RefIDQual>';
            $lines[] =    "<RefID>$poNumber</RefID>";
            $lines[] = '</RefInfo>';
        }

        if ($purposeCode == '02' || $purposeCode == '03') {
            $lines[] = '<RefInfo>';
            $lines[] =    '<RefIDQual>ON</RefIDQual>';
            $lines[] =    "<RefID>$orderNum</RefID>";
            $lines[] = '</RefInfo>';
            $lines[] = '<RefInfo>';
            $lines[] =    '<RefIDQual>IN</RefIDQual>';
            $lines[] =    "<RefID>$invoice</RefID>";
            $lines[] = '</RefInfo>';
        }

        return implode("\n", $lines);
    }
}
