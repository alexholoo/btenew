<?php

namespace Supplier\XmlApi\Synnex\PurchaseOrder;

class Response
{
    /**
     * @var string
     */
    protected $xmldoc;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var array
     */
    protected $orders;

    /**
     * @param string $xmldoc
     */
    public function __construct($xmldoc)
    {
        $this->xmldoc = $xmldoc; 
    }

    /**
     * @return array
     */
    public function parseXml()
    {
        $xml = simplexml_load_string($this->xmldoc); 

        if ($xml->OrderResponse->ErrorMessage) {
            $this->status = 'error';
            $this->errorMessage = strval($xml->OrderResponse->ErrorMessage);
            $this->errorDetail = strval($xml->OrderResponse->ErrorDetail);
            return;
        }

        $this->status = strval($xml->OrderResponse->Code);

        #this->orders['customerNumber'] = strval($xml->OrderResponse->CustomerNumber);
        $this->orders['poNumber'] = strval($xml->OrderResponse->PONumber);
        $this->orders['code'] = strval($xml->OrderResponse->Code);

        if ($xml->OrderResponse->Code == 'rejected') {
            $this->orders['reason'] = strval($xml->OrderResponse->Reason);
        }

        #echo $xml->OrderResponse->ResponseDateTime, EOL;
        #echo $xml->OrderResponse->ResponseElapsedTime, EOL;

        foreach ($xml->OrderResponse->Items as $item) {
            $order = [];

            $order['sku'] = strval($item->Item->SKU);
            $order['qty'] = strval($item->Item->OrderQuantity);
            $order['code'] = strval($item->Item->Code);

            if ($item->Item->Code == 'rejected') {
                $order['reason'] = strval($item->Item->Reason);
            }

            $order['orderNo'] = strval($item->Item->OrderNumber);
            $order['orderType'] = strval($item->Item->OrderType);
            $order['shipFrom'] = strval($item->Item->ShipFromWarehouse);
            #order['synnexRef'] = strval($item->Item->SynnexInternalReference);

            $this->orders['items'][] = $order;
        }

        return $this->orders;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getOrders()
    {
        return $this->orders;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}
