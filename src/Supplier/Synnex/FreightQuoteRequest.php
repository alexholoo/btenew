<?php

namespace Supplier\Synnex;

use Toolkit\Utils;
use Supplier\Model\Order;
use Supplier\Model\Request as BaseRequest;

class FreightQuoteRequest extends BaseRequest
{
    /**
     * @var Supplier\Model\Order
     */
    protected $order;

    /**
     * @param array $order
     */
    public function setOrder($order)
    {
        $this->order = new Order($order);
    }

    /**
     * @return string
     */
    public function toXml()
    {
        $lines = array();
        $lines[] = '<?xml version="1.0" encoding="UTF-8" ?>';
        $lines[] = '<SynnexB2B>';
        $lines[] = $this->credential();
        $lines[] = $this->freightQuoteRequest();
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

    protected function freightQuoteRequest()
    {
        $lines = array();

        $customerNo = $this->config['customerNo'];
        $zipcode    = $this->order->shippingAddress->zipcode;
        $sku        = $this->order->items[0]->sku;
        $qty        = $this->order->items[0]->qty;
        $branch     = $this->order->branch;

        if (substr($sku, 0, 4) == 'SYN-') {
            $sku = substr($sku, 4);
        }

        $lines[] = '<FreightQuoteRequest version="1.0">';
        $lines[] =    "<CustomerNumber>$customerNo</CustomerNumber>";
        $lines[] =    '<CustomerName>BTE Computer</CustomerName>';
        #lines[] =    '<RequestDateTime>2001-08-28T08:22:11</RequestDateTime>';
        $lines[] =    "<ShipFromWarehouse>$branch</ShipFromWarehouse>";
        $lines[] =    "<ShipToZipCode>$zipcode</ShipToZipCode>";
        $lines[] =    '<ShipMethodCode></ShipMethodCode>';
        $lines[] =    '<ServiceLevel></ServiceLevel>';
        $lines[] =    '<Items>';
        $lines[] =       '<Item lineNumber="1">';
        $lines[] =           "<SKU>$sku</SKU>";
        #lines[] =           '<MfgPartNumber></MfgPartNumber>';
        #lines[] =           '<Description></Description>';
        $lines[] =           "<Quantity>$qty</Quantity>";
        $lines[] =       '</Item>';
        $lines[] =    '</Items>';
        $lines[] = '</FreightQuoteRequest>';

        return implode("\n", $lines);
    }
}
