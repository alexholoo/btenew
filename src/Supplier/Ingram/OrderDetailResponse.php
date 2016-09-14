<?php

namespace Supplier\Ingram;

use Supplier\Model\Response;
use Supplier\Model\OrderDetailResult;
use Supplier\Model\OrderStatusResponse as BaseResponse;

class OrderDetailResponse extends BaseResponse
{
    /**
     * @return Supplier\Model\OrderDetailResult
     */
    public function parseXml()
    {
        $xml = simplexml_load_string($this->xmldoc);

        $result = new OrderDetailResult();

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
