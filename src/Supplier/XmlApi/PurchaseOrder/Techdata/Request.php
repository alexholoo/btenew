<?php

namespace Supplier\XmlApi\PurchaseOrder\Techdata;

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

        $lines[] = "<XML_Order_Submit>";
        $lines[] = $this->header();
        $lines[] = $this->detail();
        $lines[] = "</XML_Order_Submit>";

        return implode("\n", $lines);
    }

    public function login()
    {
        $lines = array();

        $userid = $this->config['username'];
        $passwd = $this->config['password'];

        $orderNo = $this->order['orderNo'];
        $address = $this->order['address'];
        $city = $this->order['city'];
        $state = $this->order['state'];
        $postalCode = $this->order['zipcode'];
        $country = $this->order['country'];
        $contact = $this->order['contact'];

        $lines[] = "<Header>";
        $lines[] = "  <UserName>$userid</UserName>";
        $lines[] = "  <Password>$password</Password>";
        $lines[] = "  <ResponseVersion>1.6</ResponseVersion>";
        $lines[] = "  <OrderTypeCode>BS</OrderTypeCode>"; // ??
        $lines[] = "  <PONbr>$orderNo</PONbr>";
        $lines[] = "  <SalesRequirementCode>BK</SalesRequirementCode>"; // ??
        $lines[] = "  <Name>eCOMMERCE</Name>"; // ??
        $lines[] = "  <AddrInfo>";
        $lines[] = "    <Addr>$address</Addr>";
        $lines[] = "  </AddrInfo>";
        $lines[] = "  <CityName>$city</CityName>";
        $lines[] = "  <StateProvinceCode>$state</StateProvinceCode>";
        $lines[] = "  <PostalCode>$postalCode</PostalCode>";
        $lines[] = "  <ContactName>$contact</ContactName>";
        $lines[] = "</Header>";

        return implode("\n", $lines);
    }

    public function detail()
    {
        $lines = array();

        $sku = $this->order['sku'];
        $qty = $this->order['qty'];

        if (substr($sku, 0, 3) == 'TD-') {
            $sku = substr($sku, 3);
        }

        $lines[] = "<Detail>";
        $lines[] = "  <LineInfo>";
        $lines[] = "    <QtyOrdered>$qty</QtyOrdered>";
        $lines[] = "    <ProductIDQual>VP</ProductIDQual>"; // ??
        $lines[] = "    <ProductID>$sku</ProductID>";
        $lines[] = "    <IDCode>01</IDCode>"; // ??
        $lines[] = "  </LineInfo>";
        $lines[] = "</Detail>";

        return implode("\n", $lines);
    }
}
