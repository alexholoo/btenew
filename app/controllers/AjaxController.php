<?php

namespace App\Controllers;

use Supplier\Supplier;
use App\Models\Orders;

class AjaxController extends ControllerBase
{
    public function initialize()
    {
        $this->view->disable();
    }

    public function makePurchaseAction()
    {
        if ($this->request->isPost()) {
            $orderId = $this->request->getPost('order_id');
            $sku = $this->request->getPost('sku');
            $branch = $this->request->getPost('code', null, '');
            $qty = $this->request->getPost('qty');
            $comment = $this->request->getPost('comment', null, '');
            $shipMethod = $this->request->getPost('shipMethod', null, '');
            $notifyEmail = $this->request->getPost('notifyEmail', null, '');

            $order = Orders::find("orderId='$orderId'");

            if (!$order) {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Order not found']);
                return $this->response;
            }

            if (count($order) > 1) {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Cannot handle multi-items order']);
                return $this->response;
            }

            $orderInfo = $order[0]->toArray();
            $orderInfo['sku'] = $sku; // it might be different
            #orderInfo['price'] = $this->pricelistService->getPrice($sku); // Synnex need this
            $orderInfo['branch'] = $branch;
            $orderInfo['comment'] = $comment;
            $orderInfo['shipMethod'] = $shipMethod;
            $orderInfo['notifyEmail'] = $notifyEmail;

            fpr($orderInfo);

            if (gethostname() != 'BTELENOVO') {
               #$this->response->setJsonContent(['status' => 'OK', 'data' => '112233']);
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Testing Only']);
                return $this->response;
            }

            // Make sure the order is pending (not purchased yet)
            if ($this->orderService->isOrderPurchased($orderId)) {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'The order has been purchased']);
                return $this->response;
            }

            // Make sure the order is not cancelled on amazon
            if ($this->orderService->isOrderCanceled($orderInfo)) {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'The order has been cancelled']);
                return $this->response;
            }

            // Make XmlApi call for purchasing
            try {
                $client = Supplier::createClient($sku);
                if (!$client) {
                    throw new \Exception("Cannot purchase order for $sku");
                }

                $result = $client->purchaseOrder($orderInfo);
                $status = $result->getStatus();

                if ($status == 'ERROR') {
                    $errorMessage = $result->getErrorMessage();
                    $this->response->setJsonContent(['status' => 'ERROR', 'message' => $errorMessage]);
                } else {
                    //$this->markOrderPurchased($orderId, $sku);
                    $this->response->setJsonContent(['status' => 'OK', 'data' => $result->orderNo]);
                }
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
            $sku = $this->request->getPost('sku');
            $branch = $this->request->getPost('code', null, '');

            $order = Orders::findFirst("orderId='$orderId'");
            if (!$order) {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Order not found']);
                return $this->response;
            }

            $result = $this->db->fetchOne("SELECT order_id FROM shopping_cart WHERE order_id='$orderId'");

            if ($result) {
                $this->db->updateAsDict('shopping_cart', [
                    'sku' => $sku
                ],
                "order_id='$orderId'");
            } else {
                $this->db->insertAsDict('shopping_cart', [
                    'order_id'  => $orderId,
                    'sku'       => $sku,
                    'qty'       => $order->qty
                ]);
            }

            $this->response->setJsonContent(['status' => 'OK', 'data' => 'Added to shopping cart']);
            return $this->response;
        }
    }

    public function shoppingCartCheckoutAction()
    {
        $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Testing Only']);
        return $this->response;

        //$this->runJob('job/shoppingCartCheckoutJob');
    }

    /* ===== internal methods ===== */
}
