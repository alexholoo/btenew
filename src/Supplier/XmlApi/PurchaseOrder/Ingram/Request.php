<?php

namespace Supplier\XmlApi\PurchaseOrder\Ingram;

class Request
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $order;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function addOrder($order)
    {
        $this->order = $order;
    }

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

        return implode("\n", $lines);
    }

    public function login()
    {
        $lines = array();

        $userid = $this->config['username'];
        $passwd = $this->config['password'];

        $lines[] = "<TransactionHeader>";
        $lines[] = "   <SenderID></SenderID>";
        $lines[] = "   <ReceiverID></ReceiverID>";
        $lines[] = "   <CountryCode>CA</CountryCode>";
        $lines[] = "   <LoginID>$userid</LoginID>";
        $lines[] = "   <Password>$password</Password>";
        $lines[] = "   <TransactionID></TransactionID>";
        $lines[] = "</TransactionHeader>";

        return implode("\n", $lines);
    }

    public function orderHeader()
    {
        $lines = array();

        $orderNo = $this->order['orderNo'];
        $contact = $this->order['contact'];
        $address = $this->order['address'];
        $city    = $this->order['city'];
        $state   = $this->order['state'];
        $zipcode = $this->order['zipcode'];
        $branch  = $this->order['branch'];

        $lines[] = "<OrderHeaderInformation>";
        $lines[] = "  <BillToSuffix />";
        $lines[] = "  <AddressingInformation>";
        $lines[] = "    <CustomerPO>$orderNo</CustomerPO>";
        $lines[] = "    <ShipToAttention>$contact</ShipToAttention>";
        $lines[] = "    <EndUserPO>$orderNo</EndUserPO>";
        $lines[] = "    <ShipTo>";
        $lines[] = "      <Address>";
        $lines[] = "        <ShipToAddress1></ShipToAddress1>";
        $lines[] = "        <ShipToAddress2>$address</ShipToAddress2>";
        $lines[] = "        <ShipToAddress3></ShipToAddress3>";
        $lines[] = "        <ShipToCity>$city</ShipToCity>";
        $lines[] = "        <ShipToProvince>$state</ShipToProvince>";
        $lines[] = "        <ShipToPostalCode>$zipcode</ShipToPostalCode>";
        $lines[] = "      </Address>";
        $lines[] = "    </ShipTo>";
        $lines[] = "  </AddressingInformation>";
        $lines[] = "  <ProcessingOptions>";
        $lines[] = "    <CarrierCode>PI</CarrierCode>";
        $lines[] = "    <AutoRelease>0</AutoRelease>";
        $lines[] = "    <ThirdPartyFreightAccount></ThirdPartyFreightAccount>";
        $lines[] = "    <KillOrderAfterLineError>N</KillOrderAfterLineError>";
        $lines[] = "    <ShipmentOptions>";
        $lines[] = "      <BackOrderFlag>Y</BackOrderFlag>";
        $lines[] = "      <SplitShipmentFlag>N</SplitShipmentFlag>";
        $lines[] = "      <SplitLine>N</SplitLine>";
        $lines[] = "      <ShipFromBranches>$branch</ShipFromBranches>";
        $lines[] = "      <DeliveryDate></DeliveryDate>";
        $lines[] = "    </ShipmentOptions>";
        $lines[] = "  </ProcessingOptions>";
        $lines[] = "  <DynamicMessage>";
        $lines[] = "    <MessageLines></MessageLines>";
        $lines[] = "  </DynamicMessage>";
        $lines[] = "</OrderHeaderInformation>";

        return implode("\n", $lines);
    }

    public function orderLine()
    {
        $lines = array();

        $sku = $this->order['sku'];
        $qty = $this->order['qty'];
        $comment = $this->order['comment'];

        if (substr($sku, 0, 4) == 'ING-') {
            $sku = substr($sku, 4);
        }

        $lines[] = "<OrderLineInformation>";
        $lines[] = "  <ProductLine>";
        $lines[] = "    <SKU>$sku</SKU>";
        $lines[] = "    <Quantity>$qty</Quantity>";
        $lines[] = "    <CustomerLineNumber />";
        $lines[] = "    <ReservedInventory>";
        $lines[] = "      <ReserveCode>C</ReserveCode>";
        $lines[] = "      <ReserveSequence>01</ReserveSequence>";
        $lines[] = "    </ReservedInventory>";
        $lines[] = "    <CustomerPartNumber></CustomerPartNumber>";
        $lines[] = "    <UPC></UPC>";
        $lines[] = "    <ManufacturerPartNumber></ManufacturerPartNumber>";
        $lines[] = "    <ShipFromBranchAtLine>10</ShipFromBranchAtLine>";
        $lines[] = "    <RequestedPrice></RequestedPrice>";
        $lines[] = "  </ProductLine>";
        $lines[] = "  <CommentLine>";
        $lines[] = "    <CommentText>$comment</CommentText>";
        $lines[] = "  </CommentLine>";
        $lines[] = "</OrderLineInformation>";

        return implode("\n", $lines);
    }
}
