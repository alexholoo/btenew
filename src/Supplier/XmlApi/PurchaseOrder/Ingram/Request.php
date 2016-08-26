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
        $lines[] = "  <Version>2.0</Version>";
        $lines[] = "  <TransactionHeader>";
        $lines[] = "     <SenderID>123456789</SenderID>";
        $lines[] = "     <ReceiverID>987654321</ReceiverID>";
        $lines[] = "     <CountryCode>FT</CountryCode>";
        $lines[] = "     <LoginID>CA3833HHD</LoginID>";
        $lines[] = "     <Password>Re887Jky52</Password>";
        $lines[] = "     <TransactionID>54321</TransactionID>";
        $lines[] = "  </TransactionHeader>";
        $lines[] = "  <OrderHeaderInformation>";
        $lines[] = "     <BillToSuffix />";
        $lines[] = "     <AddressingInformation>";
        $lines[] = "       <CustomerPO>CustomerPO_1</CustomerPO>";
        $lines[] = "       <ShipToAttention>Mrs Jones</ShipToAttention>";
        $lines[] = "       <EndUserPO>EndUserPO_1</EndUserPO>";
        $lines[] = "       <ShipTo>";
        $lines[] = "         <Address>";
        $lines[] = "           <ShipToAddress1>Red House Company</ShipToAddress1>";
        $lines[] = "           <ShipToAddress2>55 Elm Street</ShipToAddress2>";
        $lines[] = "           <ShipToAddress3></ShipToAddress3>";
        $lines[] = "           <ShipToCity>Toronto </ShipToCity>";
        $lines[] = "           <ShipToProvince>ON</ShipToProvince>";
        $lines[] = "           <ShipToPostalCode>SW1 3IM</ShipToPostalCode>";
        $lines[] = "         </Address>";
        $lines[] = "       </ShipTo>";
        $lines[] = "     </AddressingInformation>";
        $lines[] = "     <ProcessingOptions>";
        $lines[] = "       <CarrierCode>PI</CarrierCode>";
        $lines[] = "       <AutoRelease>0</AutoRelease>";
        $lines[] = "       <ThirdPartyFreightAccount></ThirdPartyFreightAccount>";
        $lines[] = "       <KillOrderAfterLineError>N</KillOrderAfterLineError>";
        $lines[] = "       <ShipmentOptions>";
        $lines[] = "         <BackOrderFlag>Y </BackOrderFlag>";
        $lines[] = "         <SplitShipmentFlag>N</SplitShipmentFlag>";
        $lines[] = "         <SplitLine>N</SplitLine>";
        $lines[] = "         <ShipFromBranches>20</ShipFromBranches>";
        $lines[] = "         <DeliveryDate>20090701</DeliveryDate>";
        $lines[] = "       </ShipmentOptions>";
        $lines[] = "     </ProcessingOptions>";
        $lines[] = "     <DynamicMessage>";
        $lines[] = "       <MessageLines>Please deliver to Mrs Jones</MessageLines>";
        $lines[] = "     </DynamicMessage>";
        $lines[] = "  </OrderHeaderInformation>";
        $lines[] = "  <OrderLineInformation>";
        $lines[] = "     <ProductLine>";
        $lines[] = "       <SKU>123321</SKU>";
        $lines[] = "       <Quantity>1</Quantity>";
        $lines[] = "       <CustomerLineNumber />";
        $lines[] = "       <ReservedInventory>";
        $lines[] = "          <ReserveCode>C</ReserveCode>";
        $lines[] = "          <ReserveSequence>01</ReserveSequence>";
        $lines[] = "       </ReservedInventory>";
        $lines[] = "       <CustomerPartNumber></CustomerPartNumber>";
        $lines[] = "       <UPC></UPC>";
        $lines[] = "       <ManufacturerPartNumber></ManufacturerPartNumber>";
        $lines[] = "       <ShipFromBranchAtLine>10</ShipFromBranchAtLine>";
        $lines[] = "       <RequestedPrice>25.00</RequestedPrice>";
        $lines[] = "     </ProductLine>";
        $lines[] = "     <CommentLine>";
        $lines[] = "        <CommentText>Handle with care</CommentText>";
        $lines[] = "     </CommentLine>";
        $lines[] = "  </OrderLineInformation>";
        $lines[] = "  <ShowDetail>2</ShowDetail>";
        $lines[] = "</OrderRequest>";

        return implode("\n", $lines);
    }

    public function login()
    {
        $lines = array();

        $userid = $this->config['username'];
        $passwd = $this->config['password'];


        return implode("\n", $lines);
    }

    public function orderHeader()
    {
        $lines = array();


        return implode("\n", $lines);
    }

    public function orderItems()
    {
        $lines = array();

        if (substr($sku, 0, 3) == 'ING-') {
            $sku = substr($sku, 3);
        }

        return implode("\n", $lines);
    }
}
