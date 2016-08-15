<?php
namespace App\Controllers;

class PurchaseController extends ControllerBase
{
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

    public function orderAction()
    {
        $this->view->disable();

        if ($this->request->isPost()) {
            $orderId = $this->request->getPost('order_id');
            $sku = $this->request->getPost('sku');

            // TODO: make a xmlapi call to purchase
            // make sure the order is not purchased (pending)

            $this->markOrderPurchased($orderId);

            // pass result to frontend
            $response = new \Phalcon\Http\Response();
            if (substr($sku, 0, 2) == 'TD') {
                $response->setContent(json_encode(['status' => 'error', 'message' => 'Unknown supplier']));
            } else {
                $response->setContent(json_encode(['status' => 'OK']));
            }
            return $response;
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

    protected function markOrderPurchased($orderId)
    {
        // mark the order as 'purchased' to prevent purchase twice
        $sql = "UPDATE ca_order_notes SET status='purchased' WHERE order_id=?";
        $result = $this->db->query($sql, array($orderId));
        return $result;
    }
}
