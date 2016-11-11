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
        $this->view->orderDates = $this->dropshipService->getDateListFromOrders();
        $this->view->orders = $this->dropshipService->getOrders($params);
    }
}
