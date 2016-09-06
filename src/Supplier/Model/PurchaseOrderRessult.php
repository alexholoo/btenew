<?php

namepspace Supplier\Model;

class PurchaseOrderResult
{
    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $orderNo;

    /**
     * @var string
     */
    public $errorMessage;

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
    public function getOrderNo()
    {
        return $this->orderNo;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}
