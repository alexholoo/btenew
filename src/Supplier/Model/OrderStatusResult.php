<?php

namespace Supplier\Model;

class OrderStatusResult
{
    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $errorMessage;

    /**
     * @var string
     */
    public $orderNo;

    /**
     * @var string
     */
    public $sku;

    /**
     * @var integer
     */
    public $qty;

    /**
     * @var string
     */
    public $trackingNumber;

    /**
     * @var string
     */
    public $carrier;

    /**
     * @var string
     */
    public $service;

    /**
     * @var string
     */
    public $shipDate;

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @return string
     */
    public function getOrderNo()
    {
        return $this->orderNo;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @return string
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * @return string
     */
    public function getTrackingNUmber()
    {
        return $this->trackingNumber;
    }

    /**
     * @return string
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @return string
     */
    public function getShipDate()
    {
        return $this->shipDate;
    }
}
