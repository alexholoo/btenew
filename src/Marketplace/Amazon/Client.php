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

    public function uploadTracking()
    {
        // TODO: export tracking to csv format
        try {
            $amz = new AmazonFeed($this->store);
            $amz->setFeedType('_POST_ORDER_FULFILLMENT_DATA_');
           #$amz->setFeedContent($feed);
           #$amz->loadFeedFile($path);
            $amz->submitFeed();
            return $amz->getResponse();
        } catch (Exception $ex) {
            echo 'There was a problem with the Amazon library. Error: '.$ex->getMessage();
        }
    }
}
