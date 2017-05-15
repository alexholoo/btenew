<?php

namespace Ajax\Controllers;

use Supplier\Supplier;
use Supplier\Model\Order;
use App\Models\Orders;
use Supplier\PurchaseOrderLog;

class DropshipController extends ControllerBase
{
    public function purchaseAction()
    {
        if ($this->request->isPost()) {
            $orderId     = $this->request->getPost('order_id');
            $sku         = $this->request->getPost('sku');
            $branch      = $this->request->getPost('code', null, '');
            $qty         = $this->request->getPost('qty');
            $comment     = $this->request->getPost('comment', null, '');
            $shipMethod  = $this->request->getPost('shipMethod', null, '');
            $notifyEmail = $this->request->getPost('notifyEmail', null, '');

            try {
                $order = $this->checkOrder($orderId);

                $orderInfo = $order->toArray();

                $orderInfo['sku'] = $sku; // it might be different
                $orderInfo['branch'] = $branch;
                $orderInfo['comment'] = $comment;
                $orderInfo['shipMethod'] = $shipMethod;
                $orderInfo['notifyEmail'] = $notifyEmail;

                if (!IS_PROD) {
                    throw new \Exception('Testing Only');
                }

                // Make sure the order is not cancelled on amazon
                if ($this->orderService->isOrderCanceled($orderInfo)) {
                    throw new \Exception('The order has been cancelled');
                }

                // Make XmlApi call for purchasing
                $client = Supplier::createClient($sku);
                if (!$client) {
                    throw new \Exception("Cannot purchase order for $sku");
                }

                $order = new Order($orderInfo);

                $result = $client->purchaseOrder($order);
                $status = $result->getStatus();

                if ($status == 'ERROR') {
                    throw new \Exception($result->getErrorMessage());
                }

                //$this->markOrderPurchased($orderId, $sku);
                $this->response->setJsonContent(['status' => 'OK', 'data' => $result->orderNo]);

            } catch (\Exception $e) {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => $e->getMessage()]);
            }

            return $this->response;
        }
    }

    public function markAsProcessedAction()
    {
        if ($this->request->isPost()) {
            $orderId = $this->request->getPost('orderId');

            $this->checkOrder($orderId);

            PurchaseOrderLog::markProcessed($orderId);

            $this->response->setJsonContent(['status' => 'OK', 'data' => 'The order marked processed']);
            return $this->response;
        }
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
