<?php

namespace Supplier\Synnex;

use Toolkit\Utils;
use Toolkit\CanadaProvince;
use Supplier\Model\BatchPurchaseRequest as BaseRequest;

class BatchPurchaseRequest extends BaseRequest
{
    /**
     * @return string
     */
    public function toXml()
    {
        $lines = array();
        $lines[] = '<?xml version="1.0" encoding="UTF-8" ?>';
        $lines[] = '<SynnexB2B>';
        $lines[] = $this->credential();
        $lines[] = $this->orderRequest();
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

    protected function orderRequest()
    {
        $customerNo = $this->config['customerNo'];
        $poNumber   = $this->getOrderId();
        $endUserPO  = $this->getOrderId();
        $comment    = '';

        $lines = array();
        $lines[] = '<OrderRequest>';
        $lines[] =   "<CustomerNumber>$customerNo</CustomerNumber>";
        $lines[] =   "<PONumber>$poNumber</PONumber>";
        $lines[] =   '<DropShipFlag>Y</DropShipFlag>';

        $lines[] = $this->shipment();
        #lines[] = $this->payment();

        if ($endUserPO) {
            $lines[] = "<EndUserPONumber>$endUserPO</EndUserPONumber>";
        }

        if ($comment) {
            $lines[] = "<Comment>$comment</Comment>";
        }

        $lines[] = $this->items();

        $lines[] = '</OrderRequest>';

        return implode("\n", $lines);
    }

    protected function shipment()
    {
        $address    = $this->address['address'];
        $city       = $this->address['city'];
        $state      = $this->address['province'];
        $zipcode    = $this->address['zipcode'];
        $country    = $this->address['country'];
        $contact    = $this->address['name'];
        $phone      = $this->address['phone'];
        $email      = '';

        $arr = explode("\n", wordwrap($address, 35, "\n"));
        $addr1 = $arr[0];
        $addr2 = isset($arr[1]) ? $arr[1] : '';

        $shipMethod = $this->config['shipmethod'];
        $branch = Warehouse::MARKHAM;

        $lines = array();
        $lines[] = '<Shipment>';
        $lines[] =   "<ShipFromWarehouse>$branch</ShipFromWarehouse>";
        $lines[] =   '<ShipTo>';
        $lines[] =     "<AddressName1>$contact</AddressName1>";
        #lines[] =     "<AddressName2 />";
        $lines[] =     "<AddressLine1>$addr1</AddressLine1>";
        $lines[] =     "<AddressLine2>$addr2</AddressLine2>";
        $lines[] =     "<City>$city</City>";
        $lines[] =     "<State>$state</State>";
        $lines[] =     "<ZipCode>$zipcode</ZipCode>";
        $lines[] =     "<Country>$country</Country>";
        $lines[] =   '</ShipTo>';
        $lines[] =   '<ShipToContact>';
        $lines[] =     "<ContactName>$contact</ContactName>";
        $lines[] =     "<PhoneNumber>$phone</PhoneNumber>";
        $lines[] =     "<EmailAddress>$email</EmailAddress>";
        $lines[] =   '</ShipToContact>';
        $lines[] =   '<ShipMethod>';
        $lines[] =     "<Code>$shipMethod</Code>";
        $lines[] =   '</ShipMethod>';
        $lines[] = '</Shipment>';

        return implode("\n", $lines);
    }

    protected function payment()
    {
        $accountNo = $this->config['accountNo'];

        $lines = array();
        $lines[] = '<Payment>';
        $lines[] =   '<BillTo code="'. $accountNo. '"></BillTo>';
        $lines[] = '</Payment>';

        return implode("\n", $lines);
    }

    protected function items()
    {
        $lines = array();

        $lines[] = '<Items>';

        foreach ($this->orders as $N => $order) {
            $sku   = $order['sku'];
            $price = 'PRICE'; // $order['price']; TODO
            $qty   = $order['qty'];

            if (substr($sku, 0, 4) == 'SYN-') {
                $sku = substr($sku, 4);
            }

            $lines[] = '<Item lineNumber="'.($N+1).'">';
            $lines[] =   "<SKU>$sku</SKU>";
            $lines[] =   "<UnitPrice>$price</UnitPrice>";
            $lines[] =   "<OrderQuantity>$qty</OrderQuantity>";
            $lines[] = '</Item>';
        }

        $lines[] = '</Items>';

        return implode("\n", $lines);
    }

    public function getOrderId()
    {
        if (empty($this->orderId)) {
            $this->orderId = 'SYN-'.date('Ymd-hi');
        }

        return $this->orderId;
    }
}
