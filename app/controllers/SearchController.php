<?php

namespace App\Controllers;

class SearchController extends ControllerBase
{
    public function indexAction()
    {
    }

    public function priceAvailAction()
    {
        $this->view->pageTitle = 'Price Availability';

        if ($this->request->isGet()) {
            $sku = $this->request->getQuery('sku', 'trim');
        }

        if ($this->request->isPost()) {
            $sku = $this->request->getPost('sku', 'trim');
        }

        if ($sku) {
            try {
                $skus = $this->skuService->getAltSkus($sku);

                // Make a xmlapi call to get price and availability
                $data = $this->priceAvailService->getPriceAvailability($skus);

                /* disabled, this requires php64
                // check overstock
                $info = $this->overstockService->get($sku);
                if ($info) {
                     $data[] = [
                         'sku'   => $sku,
                         'price' => $info['cost'],
                         'avail' => [
                             [ 'branch' => 'BTE OVERSTOCK', 'qty' => $info['qty'] ],
                         ]
                     ];
                }
                */

                $this->view->data = $data;
            } catch (\Exception $e) {
                $this->view->error = $e->getMessage();
            }
        }

        $this->view->sku = $sku;
    }

    public function skuAction()
    {
        $this->view->pageTitle = 'SKU Information';

        if ($this->request->isGet()) {
            $sku = $this->request->getQuery('sku', 'trim');
        }

        if ($this->request->isPost()) {
            $sku = $this->request->getPost('sku', 'trim');
        }

        if ($sku) {
            $info = $this->skuService->getMasterSku($sku);
            $this->view->data = $info;
            $this->view->desc = $this->skuService->getDescription($sku);
            $this->view->imgurl = $this->skuService->getImageUrl($sku);
        }

        $this->view->sku = $sku;
    }

    public function orderAction()
    {
        $this->view->pageTitle = 'Order Information';

        if ($this->request->isGet()) {
            $orderId = $this->request->getQuery('id', 'trim');
        }

        if ($this->request->isPost()) {
            $orderId = $this->request->getPost('id', 'trim');
        }

        $order = $this->orderService->getOrder($orderId);

        if ($order) {
            $this->view->order = $order;
            $this->view->items = $this->orderService->getOrderItems($orderId);
            $this->view->address = $this->orderService->getShippingAddress($orderId);
        }

        $this->view->id = $orderId;
    }

    public function addressAction()
    {
        $this->view->pageTitle = 'Address Information';

        if ($this->request->isGet()) {
            $key = $this->request->getQuery('key', 'trim');
        }

        if ($this->request->isPost()) {
            $key = $this->request->getPost('key', 'trim');
        }

        if ($key) {
            $orders = $this->orderService->searchOrders($key);

            if ($orders) {
                $this->view->orders = $orders;
            }
        }

        $this->view->key = $key;
    }

    // forward /search/invloc to /invloc/search
    public function invlocAction()
    {
        $this->dispatcher->forward([
            'controller' => 'invloc',
            'action'     => 'search',
        ]);
    }
}
