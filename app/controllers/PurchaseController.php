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
}
