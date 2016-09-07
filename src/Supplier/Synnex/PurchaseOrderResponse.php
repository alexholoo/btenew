<?php

namespace Supplier\Synnex;

use Supplier\Model\Response;
use Supplier\Model\PurchaseOrderResult;
use Supplier\Model\PurchaseOrderResponse as BaseResponse;

class PurchaseOrderResponse extends BaseResponse
{
    /**
     * @return Supplier\Model\PurchaseOrderResult
     */
    public function parseXml()
    {
        $xml = simplexml_load_string($this->xmldoc);

        $result = new PurchaseOrderResult();

        if ($xml->OrderResponse->ErrorMessage) {
            $this->status = 'ERROR';
            $this->errorMessage = strval($xml->OrderResponse->ErrorMessage);
            $this->errorDetail = strval($xml->OrderResponse->ErrorDetail);
            return;
        }

        $this->status = strval($xml->OrderResponse->Code);

        #this->orders['customerNumber'] = strval($xml->OrderResponse->CustomerNumber);
        $this->orders['poNumber'] = strval($xml->OrderResponse->PONumber);
        $this->orders['code'] = strval($xml->OrderResponse->Code);

        if ($xml->OrderResponse->Code == 'rejected') {
            $this->status = 'ERROR';
            $this->errorMessage = strval($xml->OrderResponse->Reason);
        }

        #echo $xml->OrderResponse->ResponseDateTime, EOL;
        #echo $xml->OrderResponse->ResponseElapsedTime, EOL;

        foreach ($xml->OrderResponse->Items as $item) {
            $order = [];

            $order['sku'] = 'SYN-' . strval($item->Item->SKU);
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
}
