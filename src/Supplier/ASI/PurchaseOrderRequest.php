<?php

namespace Supplier\ASI;

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
        $cid  = $this->config['CID'];
        $cert = $this->config['CERT'];

        $orderId = $this->order->orderId;

        $lines = array();

        $lines[] = sprintf('<ASIOrderRequest custid="%s" custpo="%s" cert="%s">', $cid, $orderId, $cert);
        $lines[] = $this->orderHeader();
        $lines[] = $this->orderDetail();
        $lines[] = '</ASIOrderRequest>';

        return Utils::formatXml(implode("\n", $lines));
    }

    protected function orderHeader()
    {
        $lines = array();

        $contact = $this->order->shippingAddress->contact;
        $address = $this->order->shippingAddress->address;
        $city    = $this->order->shippingAddress->city;
        $state   = $this->order->shippingAddress->province;
        $zipcode = $this->order->shippingAddress->zipcode;
        $phone   = $this->order->shippingAddress->phone;
        $country = $this->order->shippingAddress->country;
        $comment = $this->order->comment;

        $state = CanadaProvince::nameToCode($state);

        $arr = explode("\n", wordwrap($address, 30, "\n"));
        $addr1 = $arr[0];
        $addr2 = isset($arr[1]) ? $arr[1] . " $phone" : $phone;

        $lines[] = "<header>";
        $lines[] =   "<shipto>";
        $lines[] =     "<name>$contact</name>";
        $lines[] =     "<address1>$addr1</address1>";
        $lines[] =     "<address2>$addr2</address2>";
        $lines[] =     "<city>$city</city>";
        $lines[] =     "<state>$state</state>";
        $lines[] =     "<zip>$zipcode</zip>";
        $lines[] =     "<country>$country</country>";
        $lines[] =   "</shipto>";
        $lines[] =   "<shipinfo>";
        $lines[] =     "<via>FDG</via>";
        $lines[] =     "<instructions>$comment</instructions>";
        $lines[] =   "</shipinfo>";
        $lines[] = "</header>";

        return implode("\n", $lines);
    }

    protected function orderDetail()
    {
        $lines = array();

        $branch = $this->order->branch;

        $lines[] = "<detail>";

        foreach ($this->order->items as $item) {
            $sku = $item->sku;
            $qty = $item->qty;
            $price = $item->price;

            if (substr($sku, 0, 3) == 'AS-') {
                $sku = substr($sku, 3);
            }

            $lines[] = "<line>";
            $lines[] =   "<sku>$sku</sku>";
            $lines[] =   "<qty>$qty</qty>";
            $lines[] =   "<price>$price</price>";
            $lines[] =   "<branch>$branch</branch>";
            $lines[] = "</line>";
        }

        $lines[] = "</detail>";

        return implode("\n", $lines);
    }
}
