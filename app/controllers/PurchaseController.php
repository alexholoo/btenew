<?php
namespace App\Controllers;

use App\Models\Orders;

class PurchaseController extends ControllerBase
{
    public function initialize()
    {
        $this->view->pageTitle = 'Purchase Assistant';
    }

    public function assistAction()
    {
        $date = date('Y-m-d'); //'all';
        $stage = 'all';
        $overstock = '0';
        $express = '0';

        if ($this->request->isPost()) {
            $date = $this->request->getPost('date');
            $stage = $this->request->getPost('stage');
            $overstock = $this->request->getPost('overstock');
            $express = $this->request->getPost('express');
        }

        $this->view->date = $date;
        $this->view->stage = $stage;
        $this->view->overstock = $overstock;
        $this->view->express = $express;
        $this->view->orderDates = $this->getOrderDates();
        $this->view->orders = $this->getPurchaseOrders($date, $stage, $overstock, $express);
    }

    public function makeAction()
    {
        $this->view->disable();

        if ($this->request->isPost()) {
            $orderId = $this->request->getPost('order_id');
            $sku = $this->request->getPost('sku');
            $comment = $this->request->getPost('comment');

            // TODO: make a xmlapi call to purchase
            // make sure the order is not purchased (pending)

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
        $this->view->disable();

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

    protected function getPurchaseOrders($date, $stage, $overstock, $express)
    {
        $sql = 'SELECT * FROM ca_order_notes';

        $where = [];

        if ($date != 'all') {
            $where[] = "date = '$date'";
        }

        if ($stage != 'all') {
            $where[] = "status = '$stage'";
        }

        if ($overstock) {
            $where[] = "stock_status = 'overstock'";
        }

        if ($express) {
            $where[] = "express = 1";
        }

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $result = $this->db->query($sql);

        $data = [];
        while ($row = $result->fetch(\Phalcon\Db::FETCH_ASSOC)) {
            $row['related_sku'] = explode('|', $row['related_sku']);
            $row['related_sku'] = array_map('trim', $row['related_sku']);
            $row['related_sku'] = array_filter($row['related_sku']);

            $data[] = $row;
        }

        return $data;
    }

    protected function getOrderDates()
    {
        $sql = 'SELECT DISTINCT date FROM ca_order_notes ORDER BY date DESC';
        $result = $this->db->query($sql);

        $dates = [];
        while ($row = $result->fetch(\Phalcon\Db::FETCH_ASSOC)) {
            $dates[] = $row['date'];
        }

        return $dates;
    }

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

        // mockup of data format of price avail
        foreach ($items as $sku) {
            $data[] = [
                'sku' => $sku,
                'price' => rand(30, 200),
                'avail' => [
                    [ 'branch' => 'MISSISSAUGA', 'qty' => rand(10, 30) ],
                    [ 'branch' => 'RICHMOND',    'qty' => rand(10, 50) ],
                    [ 'branch' => 'MARKHAM',     'qty' => rand(10, 70) ],
                ]
            ];
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
