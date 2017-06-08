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
            $keyword = $this->request->getPost('keyword', 'trim');
            $tracking = $this->shipmentService->getMasterTracking($keyword);

            $this->view->keyword = $keyword;
            $this->view->data = $tracking;
        }
    }

    public function chitchatAction()
    {
        $this->view->pageTitle = 'Chitchat';

        $saved = 0;

        if ($this->request->isPost()) {
            if (($info = $this->request->getPost('Tracking'))) {
                $saved = $this->chitchatService->save($info);
            }
            else if ($this->request->getPost('Delete')) {
                $items = $this->request->getPost('Items');
                $this->chitchatService->delete($items);
            }
            else if ($this->request->getPost('Delete_All')) {
                $this->chitchatService->deleteAll();
            }
            else if ($this->request->getPost('Export')) {
                $this->chitchatService->export();
            }
        }

        $this->view->saved = $saved;
        $this->view->list  = $this->chitchatService->list();
    }

    public function reportAction()
    {
        $this->view->pageTitle = 'Shipment Report';

        $shipment = $this->shipmentService->getShipmentReport();
        $this->view->data = $shipment;
    }

    public function rateAction()
    {
        $this->view->pageTitle = 'Shipping Rate';

        if ($this->request->isPost()) {
            $orderId = $this->request->getPost('order_id', 'trim');

            $rates = $this->shipmentService->getRates($orderId);
            $this->view->data = $rates;
        }
    }
}
