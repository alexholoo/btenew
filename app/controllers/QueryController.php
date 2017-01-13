<?php

namespace App\Controllers;

class QueryController extends ControllerBase
{
    public function initialize()
    {
        $this->view->disable();
    }

    // query/order?id=ORDER_NUMBER
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

    // query/sku?id=SKU
    public function skuAction()
    {
        $sku = $this->request->getQuery('id');

        $info = $this->productService->getMasterSku($sku);

        if ($info) {
            $this->response->setJsonContent(['status' => 'OK', 'data' => $info]);
        } else {
            $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'SKU not found']);
        }

        return $this->response;
    }

    // query/mastersku?id=SKU
    public function masterSkuAction()
    {
        $this->dispatcher->forward([
            'controller' => 'query',
            'action'     => 'sku',
        ]);
    }

    // query/tracking?id=ORDER_NUMBER|TRACKING_NUMBER
    public function trackingAction()
    {
        // id can be OrderNumber OR TrackingNumber
        $id = $this->request->getQuery('id');

        $info = $this->shipmentService->getMasterTracking($id);

        if ($info) {
            $this->response->setJsonContent(['status' => 'OK', 'data' => $info]);
        } else {
            $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Tracking not found']);
        }

        return $this->response;
    }

    // query/shippingeasy?id=ORDER_NUMBER|TRACKING_NUMBER
    public function shippingEasyAction()
    {
        // id can be OrderNumber OR TrackingNumber
        $id = $this->request->getQuery('id');

        $info = $this->shipmentService->getShippingEasy($id);

        if ($info) {
            $this->response->setJsonContent(['status' => 'OK', 'data' => $info]);
        } else {
            $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Tracking not found']);
        }

        return $this->response;
    }
}
