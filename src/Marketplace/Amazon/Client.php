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

    /**
     * @param string $mode  "Created" or "Modified"
     * @param string $start
     */
    public function getOrderList($mode = 'Modified', $start = '-24 hours')
    {
        try {
            $amz = new \AmazonOrderList($this->store);
            $amz->setLimits($mode, $start);
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

    public function uploadPriceQuantity($file)
    {
        try {
            $api = new AmazonFeed($this->store);
            $api->setFeedType('_POST_FLAT_FILE_PRICEANDQUANTITYONLY_UPDATE_DATA_');
            $api->loadFeedFile($file);
            $api->submitFeed();
            return $api->getResponse();
        } catch (Exception $ex) {
            echo 'There was a problem with the Amazon library. Error: '.$ex->getMessage();
        }
    }
}
