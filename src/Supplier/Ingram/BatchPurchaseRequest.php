<?php

namespace Supplier\Ingram;

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

        $lines[] = "<OrderRequest>";
        $lines[] = "<Version>2.0</Version>";
        $lines[] = $this->login();
        $lines[] = $this->orderHeader();
        $lines[] = $this->orderLine();
        $lines[] = "<ShowDetail>2</ShowDetail>";
        $lines[] = "</OrderRequest>";

        return Utils::formatXml(implode("\n", $lines));
    }

    public function login()
    {
        $lines = array();

        $userid = $this->config['loginId'];
        $passwd = $this->config['password'];

        $lines[] = "<TransactionHeader>";
        $lines[] =    "<SenderID></SenderID>";
        $lines[] =    "<ReceiverID></ReceiverID>";
        $lines[] =    "<CountryCode>FT</CountryCode>";  // FT=>CA, MD=>US
        $lines[] =    "<LoginID>$userid</LoginID>";
        $lines[] =    "<Password>$passwd</Password>";
        $lines[] =    "<TransactionID></TransactionID>";
        $lines[] = "</TransactionHeader>";

        return implode("\n", $lines);
    }

    public function orderHeader()
    {
        $lines = array();

        $orderId = $this->getOrderId();

        $contact = $this->address['name'];
        $address = $this->address['address'];
        $city    = $this->address['city'];
        $state   = $this->address['province'];
        $zipcode = $this->address['zipcode'];

        $branch  = Warehouse::TORONTO;

        $arr = explode("\n", wordwrap($address, 35, "\n"));
        $addr1 = $arr[0];
        $addr2 = isset($arr[1]) ? $arr[1] : '';

        $autoRelease   = $this->config['autoRelease'];
        $carrierCode   = $this->config['carrierCode'];
        $backOrder     = $this->config['backOrder'];
        $splitShipment = $this->config['splitShipment'];
        $splitLine     = $this->config['splitLine'];

        $customerPO    = $orderId;
        $endUserPO     = $orderId;

        $lines[] = "<OrderHeaderInformation>";
        $lines[] =   "<BillToSuffix />";
        $lines[] =   "<AddressingInformation>";
        $lines[] =     "<CustomerPO>$customerPO</CustomerPO>";
        $lines[] =     "<ShipToAttention></ShipToAttention>";
        $lines[] =     "<EndUserPO>$endUserPO</EndUserPO>";
        $lines[] =     "<ShipTo>";
        $lines[] =       "<Address>";
        $lines[] =         "<ShipToAddress1>$contact</ShipToAddress1>";
        $lines[] =         "<ShipToAddress2>$addr1</ShipToAddress2>";
        $lines[] =         "<ShipToAddress3>$addr2</ShipToAddress3>";
        $lines[] =         "<ShipToCity>$city</ShipToCity>";
        $lines[] =         "<ShipToProvince>$state</ShipToProvince>";
        $lines[] =         "<ShipToPostalCode>$zipcode</ShipToPostalCode>";
        $lines[] =       "</Address>";
        $lines[] =     "</ShipTo>";
        $lines[] =   "</AddressingInformation>";
        $lines[] =   "<ProcessingOptions>";
        $lines[] =     "<CarrierCode>$carrierCode</CarrierCode>";
        $lines[] =     "<AutoRelease>$autoRelease</AutoRelease>";
       #$lines[] =     "<ThirdPartyFreightAccount></ThirdPartyFreightAccount>";
       #$lines[] =     "<KillOrderAfterLineError>N</KillOrderAfterLineError>";
        $lines[] =     "<ShipmentOptions>";
        $lines[] =       "<BackOrderFlag>$backOrder</BackOrderFlag>";
        $lines[] =       "<SplitShipmentFlag>$splitShipment</SplitShipmentFlag>";
        $lines[] =       "<SplitLine>$splitLine</SplitLine>";
        $lines[] =       "<ShipFromBranches>$branch</ShipFromBranches>";
        $lines[] =       "<DeliveryDate></DeliveryDate>";
        $lines[] =     "</ShipmentOptions>";
        $lines[] =   "</ProcessingOptions>";
        $lines[] =   "<DynamicMessage>";
        $lines[] =     "<MessageLines></MessageLines>";
        $lines[] =   "</DynamicMessage>";
        $lines[] = "</OrderHeaderInformation>";

        return implode("\n", $lines);
    }

    public function orderLine()
    {
        $lines = array();

        $lines[] = "<OrderLineInformation>";

        foreach ($this->orders as $order) {
            $sku = $order['sku'];
            $qty = $order['qty'];

            $comment = '';
            $branch  = Warehouse::TORONTO;

            if (substr($sku, 0, 4) == 'ING-') {
                $sku = substr($sku, 4);
            }

            $lines[] = "<ProductLine>";
            $lines[] =   "<SKU>$sku</SKU>";
            $lines[] =   "<Quantity>$qty</Quantity>";
            $lines[] =   "<CustomerLineNumber />";
           #$lines[] =   "<ReservedInventory>";
           #$lines[] =     "<ReserveCode></ReserveCode>"; // ??
           #$lines[] =     "<ReserveSequence></ReserveSequence>"; // ??
           #$lines[] =   "</ReservedInventory>";
           #$lines[] =   "<CustomerPartNumber></CustomerPartNumber>";
           #$lines[] =   "<UPC></UPC>";
           #$lines[] =   "<ManufacturerPartNumber></ManufacturerPartNumber>";
           #$lines[] =   "<ShipFromBranchAtLine>$branch</ShipFromBranchAtLine>";
           #$lines[] =   "<RequestedPrice></RequestedPrice>";
            $lines[] = "</ProductLine>";
            $lines[] = "<CommentLine>";
            $lines[] =   "<CommentText>$comment</CommentText>";
            $lines[] = "</CommentLine>";
        }

        $lines[] = "</OrderLineInformation>";

        return implode("\n", $lines);
    }

    public function getOrderId()
    {
        if (empty($this->orderId)) {
            $this->orderId = 'ING-'.date('Ymd-hi');
        }

        return $this->orderId;
    }
}
