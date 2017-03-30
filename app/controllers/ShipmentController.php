<?php

namespace App\Controllers;

class ShipmentController extends ControllerBase
{
    public function searchAction()
    {
        $this->view->pageTitle = 'Shipment Search';

        $this->view->data = [];
        $this->view->keyword = '';

        if ($this->request->isPost()) {
            $keyword = $this->request->getPost('keyword');
            $tracking = $this->shipmentService->getMasterTracking($keyword);

            $this->view->keyword = $keyword;
            $this->view->data = $tracking;
        }
    }
}
