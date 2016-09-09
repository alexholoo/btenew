<?php

namespace Supplier\Ingram;

use Utility\Utils;
use Supplier\Model\PurchaseOrderRequest as BaseRequest;

class PurchaseOrderRequest extends BaseRequest
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

        $orderId = $this->order->orderId;
        $contact = $this->order->contact;
        $address = $this->order->address;
        $city    = $this->order->city;
        $state   = $this->order->province;
        $zipcode = $this->order->zipcode;
        $branch  = $this->order->branch;

        $autoRelease   = $this->config['autoRelease'];
        $carrierCode   = $this->config['carrierCode'];
        $backOrder     = $this->config['backOrder'];
        $splitShipment = $this->config['splitShipment'];
        $splitLine     = $this->config['splitLine'];

        $fakeOrderId = OrderNumberMapper::getFakeOrderNo($orderId);

        $customerPO    = $fakeOrderId;
        $endUserPO     = $fakeOrderId;

        if ($this->order->customerPO) {
            $customerPO = $this->order->customerPO;
        }

        if ($this->order->endUserPO) {
            $endUserPO = $this->order->endUserPO;
        }

        $lines[] = "<OrderHeaderInformation>";
        $lines[] =   "<BillToSuffix />";
        $lines[] =   "<AddressingInformation>";
        $lines[] =     "<CustomerPO>$customerPO</CustomerPO>";
        $lines[] =     "<ShipToAttention></ShipToAttention>";
        $lines[] =     "<EndUserPO>$endUserPO</EndUserPO>";
        $lines[] =     "<ShipTo>";
        $lines[] =       "<Address>";
        $lines[] =         "<ShipToAddress1>$contact</ShipToAddress1>";
        $lines[] =         "<ShipToAddress2>$address</ShipToAddress2>";
        $lines[] =         "<ShipToAddress3></ShipToAddress3>";
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

        $sku = $this->order->sku;
        $qty = $this->order->qty;
        $comment = $this->order->comment;
        $branch  = $this->order->branch;

        if (substr($sku, 0, 4) == 'ING-') {
            $sku = substr($sku, 4);
        }

        $lines[] = "<OrderLineInformation>";
        $lines[] =   "<ProductLine>";
        $lines[] =     "<SKU>$sku</SKU>";
        $lines[] =     "<Quantity>$qty</Quantity>";
        $lines[] =     "<CustomerLineNumber />";
       #$lines[] =     "<ReservedInventory>";
       #$lines[] =       "<ReserveCode></ReserveCode>"; // ??
       #$lines[] =       "<ReserveSequence></ReserveSequence>"; // ??
       #$lines[] =     "</ReservedInventory>";
       #$lines[] =     "<CustomerPartNumber></CustomerPartNumber>";
       #$lines[] =     "<UPC></UPC>";
       #$lines[] =     "<ManufacturerPartNumber></ManufacturerPartNumber>";
       #$lines[] =     "<ShipFromBranchAtLine>$branch</ShipFromBranchAtLine>";
       #$lines[] =     "<RequestedPrice></RequestedPrice>";
        $lines[] =   "</ProductLine>";
        $lines[] =   "<CommentLine>";
        $lines[] =     "<CommentText>$comment</CommentText>";
        $lines[] =   "</CommentLine>";
        $lines[] = "</OrderLineInformation>";

        return implode("\n", $lines);
    }
}
