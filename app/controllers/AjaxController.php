<?php
namespace App\Controllers;

use App\Models\Orders;
use Supplier\XmlApi\PriceAvailability\Factory as PriceAvailFactory;
use Supplier\XmlApi\PurchaseOrder\Factory as PurchaseOrderFactory;

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
            $branch = $this->request->getPost('branch', null, '');
            $qty = $this->request->getPost('qty');
            $comment = $this->request->getPost('comment');

            $order = Orders::findFirst("orderId='$orderId'");
            if ($order) {
                $orderInfo = $order->toArray();
                $orderInfo['branch'] = $branch;
                $orderInfo['comment'] = $comment;
                fpr($orderInfo);
            }

            // TODO: make a xmlapi call to purchase
            // make sure the order is not purchased (pending)
            // make sure the order is not cancelled on amazon

            // pass result to frontend
            if (substr($sku, 0, 2) == 'TD') {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Unknown supplier']);
            } else {
                $this->markOrderPurchased($orderId, $sku);
                $this->response->setJsonContent(['status' => 'OK']);
            }
            return $this->response;
        }
    }

    public function priceAvailAction()
    {
        if ($this->request->isPost()) {
            $sku = $this->request->getPost('sku');

            // TODO: make a xmlapi call to get price and availability

            $data = $this->getPriceAvailability($sku);

            // $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Unknown supplier']);
            $this->response->setJsonContent(['status' => 'OK', 'data' => $data]);

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

    /* ===== internal methods ===== */

    protected function markOrderPurchased($orderId, $sku)
    {
        // mark the order as 'purchased' to prevent purchase twice
        $sql = "UPDATE ca_order_notes SET status='purchased', actual_sku=? WHERE order_id=?";
        $result = $this->db->query($sql, array($sku, $orderId));
        return $result;
    }

    protected function getPriceAvailability($items)
    {
        $data = [];

        $factory = new PriceAvailFactory($this->config);

        foreach ($items as $sku) {
            $client = $factory->createClient($sku);
            $request = $client->createRequest();
            $request->addPartnum($sku);
            $response = $client->sendRequest($request);

            $data[] = $response->getItems();

            // mockup data format of price avail
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
