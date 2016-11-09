<?php
namespace App\Controllers;

use App\Models\Orders;

class PurchaseController extends ControllerBase
{
    public function initialize()
    {
        $this->view->pageTitle = 'Purchase Assistant';
    }

    public function indexAction()
    {
        $params = [
            'date' => date('Y-m-d'), //'all';
            'stage' => 'all',
            'overstock' => '0',
            'express' => '0',
            'orderId' => '',
        ];

        if ($this->request->isPost()) {
            $params['date'] = $this->request->getPost('date');
            $params['stage'] = $this->request->getPost('stage');
            $params['overstock'] = $this->request->getPost('overstock');
            $params['express'] = $this->request->getPost('express');
            $params['orderId'] = $this->filter->sanitize($this->request->getPost('orderId'), 'trim');
        }

        $this->view->date = $params['date'];
        $this->view->stage = $params['stage'];
        $this->view->overstock = $params['overstock'];
        $this->view->express = $params['express'];
        $this->view->orderDates = $this->getOrderDates();
        $this->view->orders = $this->getPurchaseOrders($params);
    }

    protected function getPurchaseOrders($params)
    {
        $date      = $params['date'];
        $stage     = $params['stage'];
        $overstock = $params['overstock'];
        $express   = $params['express'];
        $orderId   = $params['orderId'];

        $sql = 'SELECT * FROM ca_order_notes';

        $where = [];

        if ($date != 'all') {
            $where[] = "date = '$date'";
        }

        #if ($stage != 'all') {
        #    $where[] = "status = '$stage'";
        #}

        if ($overstock) {
            $where[] = "stock_status = 'overstock'";
        }

        if ($express) {
            $where[] = "express = 1";
        }

        if ($orderId) {
            $where = []; // ignore all other conditions
            $where[] = "order_id LIKE '%$orderId' ORDER BY id DESC";
        }

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $result = $this->db->query($sql);

        $data = [];
        while ($row = $result->fetch(\Phalcon\Db::FETCH_ASSOC)) {
            $row['status'] = 'pending';

            if (($purchase = $this->isOrderPurchased($row['order_id']))) {
                $row['status'] = 'purchased';
                $row['actual_sku'] = $purchase['sku'];
            }

            $row['related_sku'] = explode('|', $row['related_sku']);
            $row['related_sku'] = array_map('trim', $row['related_sku']);
            $row['related_sku'] = array_filter($row['related_sku']);

            if ($stage == 'all' || $stage == $row['status']) {
                $data[] = $row;
            }
        }

        return $data;
    }

    protected function getOrderDates()
    {
        $sql = 'SELECT DISTINCT date FROM ca_order_notes ORDER BY date DESC LIMIT 30';
        $result = $this->db->query($sql);

        $dates = [];
        while ($row = $result->fetch(\Phalcon\Db::FETCH_ASSOC)) {
            $dates[] = $row['date'];
        }

        return $dates;
    }

    protected function isOrderPurchased($orderId)
    {
        $sql = "SELECT orderid, sku FROM purchase_order_log WHERE orderid='$orderId'";
        $result = $this->db->fetchOne($sql);
        return $result;
    }
}
