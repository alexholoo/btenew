<?php
namespace App\Controllers;

class PurchaseController extends ControllerBase
{
    public function assistAction()
    {
        $date = 'all';
        $stage = 'all';
        $overstock = '0';
        $express = '0';

        // get orders
        $sql = 'SELECT * FROM ca_order_notes';

        $where = [];
        if ($this->request->isPost()) {
            $date = $this->request->getPost('date');
            $stage = $this->request->getPost('stage');
            $overstock = $this->request->getPost('overstock');
            $express = $this->request->getPost('express');

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

        // get all order dates
        $sql = 'SELECT DISTINCT date FROM ca_order_notes ORDER BY date DESC';
        $result = $this->db->query($sql);

        $orderDates = [];
        while ($row = $result->fetch(\Phalcon\Db::FETCH_ASSOC)) {
            $orderDates[] = $row['date'];
        }

        // pass data to view
        $this->view->date = $date;
        $this->view->orderDates = $orderDates;
        $this->view->stage = $stage;
        $this->view->overstock = $overstock;
        $this->view->express = $express;
        $this->view->data = $data;
    }

    public function orderAction()
    {
        $this->view->disable();

        if ($this->request->isPost()) {
            $orderId = $this->request->getPost('order_id');
            // TODO: make a xmlapi call to purchase
            // make sure the order is not purchased (pending)

            // mark the order as 'purchased' to prevent purchase twice
            $sql = "UPDATE ca_order_notes SET status='purchased' WHERE order_id=?";
            $result = $this->db->query($sql, array($orderId));

            // pass result to frontend
            $response = new \Phalcon\Http\Response();
            $response->setContent(json_encode(['status' => 'OK']));
            //$response->setContent(json_encode(['status' => 'error', 'message' => 'Oops! Something went wrong']));
            return $response;
        }
    }
}
