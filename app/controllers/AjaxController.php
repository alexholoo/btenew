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

            $order = Orders::findFirst("orderId='$orderId'");
            if (!$order) {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Order not found']);
                return $this->response;
            }

            $orderInfo = $order->toArray();
            $orderInfo['sku'] = $sku; // it might be different
            #orderInfo['price'] = $this->pricelistService->getPrice($sku); // Synnex need this
            $orderInfo['branch'] = $branch;
            $orderInfo['comment'] = $comment;
            $orderInfo['shipMethod'] = $shipMethod;
             fpr($orderInfo);

#$this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Testing']);
#return $this->response;

#// TODO: temp code
#if ((substr($sku, 0, 3) != 'SYN') && (substr($sku, 0, 3) != 'ING')) {
#    $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Unknown supplier']);
#    return $this->response;
#}

            // Make sure the order is pending (not purchased yet)
            if ($this->isOrderPurchased($orderId)) {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'The order has been purchased']);
                return $this->response;
            }

            // Make sure the order is not cancelled on amazon
            if ($this->isOrderCanceled($orderInfo)) {
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
                    $this->markOrderPurchased($orderId, $sku);
                    $this->response->setJsonContent(['status' => 'OK']);
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
                $data = $this->getPriceAvailability($sku);

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

            $data = $this->getOrderDetail($orderId);

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

    /* ===== internal methods ===== */

    protected function markOrderPurchased($orderId, $sku)
    {
        // mark the order as 'purchased' to prevent purchase twice
        $sql = "UPDATE ca_order_notes SET status='purchased', actual_sku=? WHERE order_id=?";
        $result = $this->db->query($sql, array($sku, $orderId));
        return $result;
    }

    protected function isOrderPending($orderId)
    {
        $sql = "SELECT status FROM ca_order_notes WHERE order_id=? LIMIT 1";
        $result = $this->db->query($sql, array($orderId))->fetch();
        return $result['status'] == 'pending';
    }

    protected function isOrderPurchased($orderId)
    {
        $sql = "SELECT status FROM ca_order_notes WHERE order_id=? LIMIT 1";
        $result = $this->db->query($sql, array($orderId))->fetch();
        return $result['status'] == 'purchased';
    }

    protected function isOrderCanceled($order)
    {
        $channel = $order['channel'];
        $orderId = $order['orderId'];

        if (substr($channel, 0, 6) != 'Amazon') {
            return false; // not cancelled
        }

        return $this->amazonService->isOrderCanceled($order);
    }

    protected function getPriceAvailability($items)
    {
        $data = [];

        foreach ($items as $sku) {
            $client = Supplier::createClient($sku);
            if ($client) {
                $result = $client->getPriceAvailability($sku);
                $data[] = $result->getFirst()->toArray();
            }

            #$data[] = [
            #    'sku' => $sku,
            #    'price' => rand(30, 200),
            #    'avail' => [
            #        [ 'branch' => 'MISSISSAUGA', 'qty' => rand(10, 30) ],
            #        [ 'branch' => 'RICHMOND',    'qty' => rand(10, 50) ],
            #        [ 'branch' => 'MARKHAM',     'qty' => rand(10, 70) ],
            #    ]
            #];
        }

        return $this->sortPriceAvailability($data);
    }

    protected function sortPriceAvailability($data)
    {
        $lowPriceFirst = function($a, $b) {
            if ($a['price'] == $b['price']) {
                return 0;
            }
            return ($a['price'] < $b['price']) ? -1 : 1; // ASC
        };

        $maxQtyFirst = function($a, $b) {
            if ($a['qty'] == $b['qty']) {
                return 0;
            }
            return ($a['qty'] < $b['qty']) ? 1 : -1; // DESC
        };

        foreach ($data as &$item) {
            usort($item['avail'], $maxQtyFirst);
        }

        usort($data, $lowPriceFirst);

        return $data;
    }

    protected function getOrderDetail($orderId)
    {
        $order = Orders::findFirst("orderId='$orderId'");
        if ($order) {
            return array_map("utf8_encode", $order->toArray());
        }
        return false;
    }
}
