<?php

namespace App\Controllers;

class PurchaseController extends ControllerBase
{
    public function assistAction()
    {
        $params = [
            'date'      => date('Y-m-d'), //'all';
            'status'    => 'all',
            'overstock' => '0',
            'express'   => '0',
            'multitem'  => '0',
            'orderId'   => '',
        ];

        if ($this->request->isPost()) {
            $params['date']      = $this->request->getPost('date');
            $params['status']    = $this->request->getPost('status');
            $params['overstock'] = $this->request->getPost('overstock');
            $params['express']   = $this->request->getPost('express');
            $params['multitem']  = $this->request->getPost('multitem');
            $params['orderId']   = $this->filter->sanitize($this->request->getPost('orderId'), 'trim');
        }

        $this->view->pageTitle  = 'Purchase Assistant';
        $this->view->date       = $params['date'];
        $this->view->status     = $params['status'];
        $this->view->overstock  = $params['overstock'];
        $this->view->express    = $params['express'];
        $this->view->multitem   = $params['multitem'];
        $this->view->orderDates = $this->dropshipService->getDateListFromOrders();
        $this->view->orders     = $this->dropshipService->getOrders($params);
    }
}
