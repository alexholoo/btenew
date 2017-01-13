<?php

namespace App\Controllers;

class QueryController extends ControllerBase
{
    public function initialize()
    {
        $this->view->disable();
    }

    public function orderAction()
    {
        $orderId = $this->request->getQuery('id');

        $order = $this->orderService->getOrder($orderId);
        $items = $this->orderService->getOrderItems($orderId);
        $shippingAddress = $this->orderService->getShippingAddress($orderId);

        if ($order) {
            $order['items'] = $items;
            $order['shippingAddress'] = $shippingAddress;

            $this->response->setJsonContent(['status' => 'OK', 'data' => $order]);
        } else {
            $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Order not found']);
        }

        return $this->response;
    }

    public function skuAction()
    {
        $sku = $this->request->getQuery('sku');

        $info = $this->productService->getMasterSku($sku);

        if ($info) {
            $this->response->setJsonContent(['status' => 'OK', 'data' => $info]);
        } else {
            $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'SKU not found']);
        }

        return $this->response;
    }

    public function masterSkuAction()
    {
        $this->dispatcher->forward([
            'controller' => 'query',
            'action'     => 'sku',
        ]);
    }

    public function trackingAction()
    {
        // service?
    }

    public function shippingAction()
    {
        // service?
    }
}
