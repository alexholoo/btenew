<?php

namespace Marketplace\Amazon;

class Client
{
    protected $store;

    public function __construct($store = null)
    {
        $this->store = $store;
    }

    public function setStore($store)
    {
        $this->store = $store;
    }

    public function getStore()
    {
        return $this->store;
    }

    public function getOrderList()
    {
        try {
            $amz = new \AmazonOrderList($this->store);
            $amz->setLimits('Modified', "-24 hours");
            $amz->setFulfillmentChannelFilter("MFN");
            $amz->setOrderStatusFilter([
                "Unshipped",
                "PartiallyShipped",
                "Canceled",
                "Unfulfillable"
            ]);
            $amz->setUseToken();
            $amz->fetchOrders();

            return $amz->getList();
        } catch (\Exception $ex) {
            echo 'There was a problem with the Amazon library. Error: '.$ex->getMessage();
        }
    }
}
