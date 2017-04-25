<?php

namespace Supplier\ASI;

use Supplier\Model\Response;
use Supplier\Model\OrderStatusResult;
use Supplier\Model\OrderStatusResponse as BaseResponse;

class OrderStatusResponse extends BaseResponse
{
    protected $shipMethods = [
        'FDG' => [ 'carrier' => 'Fedex',     'service' => 'Ground' ],
        'PGD' => [ 'carrier' => 'Purolator', 'service' => 'Ground' ],
        'UP1' => [ 'carrier' => 'UPS',       'service' => 'Next Day Saver' ],
    ];

    /**
     * @return Supplier\Model\OrderStatusResult
     */
    public function parse()
    {
        $xml = simplexml_load_string($this->xmldoc);

        $result = new OrderStatusResult();

        $result->status         = Response::STATUS_OK;
        $result->poNum          = strval($xml->order['id']);
        $result->orderNo        = strval($xml->order['po']);
        $result->invoice        = '';
        $result->sku            = '';
        $result->qty            = '';
        $result->carrier        = strval($xml->order->tracknum['shipvia']);
        $result->service        = '';
        $result->trackingNumber = strval($xml->order->tracknum);
        $result->shipDate       = '';

        if (!$result->poNum || !$result->carrier) {
            $result->status = Response::STATUS_ERROR;
            $result->errorMessage = $result->trackingNumber;
            $result->trackingNumber = '';
        }

        if (isset($this->shipMethods[$result->carrier])) {
            $info = $this->shipMethods[$result->carrier];
            $result->carrier = $info['carrier'];
            $result->service = $info['service'];
        }

        return $result;
    }
}
