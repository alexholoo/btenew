<?php

namespace App\Controllers;

use Supplier\Supplier;
use Supplier\Model\Order;
use App\Models\Orders;
use Supplier\PurchaseOrderLog;

class AjaxController extends ControllerBase
{
    public function initialize()
    {
        $this->view->disable();
    }

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

    public function makePurchaseAction()
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

                if (gethostname() != 'BTELENOVO') {
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

    public function priceAvailAction()
    {
        if ($this->request->isPost()) {
            $sku = $this->request->getPost('sku');

            try {
                // Make a xmlapi call to get price and availability
                $data = $this->priceAvailService->getPriceAvailability($sku);

                // $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Unknown supplier']);
                $this->response->setJsonContent(['status' => 'OK', 'data' => $data]);
            } catch (\Exception $e) {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => $e->getMessage()]);
            }

            return $this->response;
        }
    }

    public function orderDetailAction()
    {
        $this->view->disable();

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

    public function priceListDetailAction()
    {
    }

    public function freightQuoteAction()
    {
        if ($this->request->isPost()) {
            $orderId = $this->request->getPost('order_id');
            $sku = $this->request->getPost('sku');
            $branch = $this->request->getPost('code', null, '');

            $order = Orders::findFirst("orderId='$orderId'");
            if (!$order) {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Order not found']);
                return $this->response;
            }

            $orderInfo = $order->toArray();
            $orderInfo['sku'] = $sku; // it might be different
            $orderInfo['branch'] = $branch;

            try {
               #$client = Supplier::createClient($sku);
                $client = new \Supplier\Synnex\Client();

                $result = $client->getFreightQuote($orderInfo);
                $html = $result->toHtml('ship_method');

                $this->response->setJsonContent(['status' => 'OK', 'data' => $html]);
            } catch (\Exception $e) {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => $e->getMessage()]);
            }

            return $this->response;
        }
    }

    public function shoppingCartAddAction()
    {
        if ($this->request->isPost()) {
            $orderId = $this->request->getPost('order_id');
            $sku     = $this->request->getPost('sku');
            $branch  = $this->request->getPost('code', null, '');

            try {
                $order = $this->checkOrder($orderId);

                // TODO: refactor the code below
                $inCart = $this->shoppingCartService->findOrder($orderId);

                if ($inCart) {
                    $affectRows = $this->shoppingCartService->removeOrder($orderId);
                    $this->response->setJsonContent(['status' => 'OK', 'data' => 1-$affectRows]);
                } else {
                    $this->shoppingCartService->addOrder([
                        'orderId' => $orderId,
                        'sku'     => $sku,
                        'qty'     => $order->qty
                    ]);
                    $this->response->setJsonContent(['status' => 'OK', 'data' => 1]);
                }
            } catch (\Exception $e) {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => $e->getMessage()]);
            }

            return $this->response;
        }
    }

    public function shoppingCartCheckoutAction()
    {
        $this->runJob('job/shoppingCartCheckoutJob');

        $this->response->setJsonContent(['status' => 'OK', 'data' => 'Checkout job is running']);
        return $this->response;
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
}
