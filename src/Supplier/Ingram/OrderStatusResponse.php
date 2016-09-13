<?php

namespace Supplier\Ingram;

use Supplier\Model\Response;
use Supplier\Model\OrderStatusResult;
use Supplier\Model\OrderStatusResponse as BaseResponse;

class OrderStatusResponse extends BaseResponse
{
    /**
     * @return Supplier\Model\OrderStatusResult
     */
    public function parseXml()
    {
        $xml = simplexml_load_string($this->xmldoc);

        $result = new OrderStatusResult();

        $result->status = ''; // strval($xml->STATUS);
        $result->orderNo = ''; // strval($xml->ORDERNUM);
        $result->errorMessage = ''; // strval($xml->MESSAGE);

        if ($result->status == 'success') {
            $result->status = Response::STATUS_OK;
        }

        if ($result->status == 'failure') {
            $result->status = Response::STATUS_ERROR;
        }

        return $result;
    }
}
