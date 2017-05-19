<?php

namespace Ajax\Controllers;

class OrderController extends ControllerBase
{
    /**
     * This method was for dropship page, it will deprecate, use infoAction instead.
     *
     * /ajax/order/detail
     */
    public function detailAction()
    {
        if ($this->request->isPost()) {
            $orderId = $this->request->getPost('orderId');

            $data = $this->orderService->getOrderDetail($orderId);

            if ($data) {
                $this->response->setContentType('application/json', 'utf-8');
                $this->response->setJsonContent(['status' => 'OK', 'data' => $data]);
            } else {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Order not found']);
            }

            return $this->response;
        }
    }

    /**
     * /ajax/order/info
     */
    public function infoAction()
    {
        if ($this->request->isPost()) {
            $orderId = $this->request->getPost('orderId');

            // This might return more than one orders
            $order = $this->orderService->searchOrders($orderId);

            // For now, we just display first order, first item
            if ($order) {
                $this->response->setContentType('application/json', 'utf-8');
                $this->response->setJsonContent(['status' => 'OK', 'data' => $order[0]]);
            } else {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Order not found']);
            }

            return $this->response;
        }
    }
}
