<?php

namespace Ajax\Controllers;

use App\Models\Orders;

class ShoppingCartController extends ControllerBase
{
    public function addAction()
    {
        if ($this->request->isPost()) {
            $orderId = $this->request->getPost('order_id');
            $sku     = $this->request->getPost('sku');

            try {
                $order = $this->checkOrder($orderId);

                $inCart = $this->shoppingCartService->addOrder([
                    'orderId' => $orderId,
                    'sku'     => $sku,
                    'qty'     => $order->qty
                ]);

                $this->response->setJsonContent(['status' => 'OK', 'data' => $inCart]);

            } catch (\Exception $e) {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => $e->getMessage()]);
            }

            return $this->response;
        }
    }

    public function checkoutAction()
    {
        $this->runJob('job/shoppingCartCheckoutJob');

        $this->response->setJsonContent(['status' => 'OK', 'data' => 'Checkout job is running']);
        return $this->response;
    }

    /* ===== internal methods ===== */

    protected function checkOrder($orderId)
    {
        $order = Orders::find("orderId='$orderId'");

        if (!$order) {
            throw new \Exception('Order not found');
        }

        if (count($order) > 1) {
            throw new \Exception('Cannot handle multi-items order');
        }

        // Make sure the order is pending (not purchased yet)
        if ($this->orderService->isOrderPurchased($orderId)) {
            throw new \Exception('The order has been purchased');
        }

        return $order[0];
    }
}
