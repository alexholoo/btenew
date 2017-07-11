<?php

namespace Api\Controllers;

class ShipmentController extends ControllerBase
{
    public function initialize()
    {
        $this->view->disable();
    }

    public function newAction()
    {
        //$userAgent = $this->request->getUserAgent();
        //if ($userAgent != 'BTE Barcode Scanner')

        if ($this->request->isPost()) {
            $trackingNum = $this->request->getPost('trackingnum');
            $user = $this->request->getPost('user');

            if ($trackingNum && $user) {
                $info = $this->shipmentService->addShipment($trackingNum, $user);
                $this->response->setJsonContent(['status' => 'OK', 'data' => $info]);
            } else {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Bad Request']);
            }

            return $this->response;
        }
    }

    public function orderSnAction()
    {
        if ($this->request->isPost()) {
            $orderId = $this->request->getPost('orderno');
            $sn = $this->request->getPost('sn');

            if ($orderId && $sn) {
                $info = $this->shipmentService->saveOrderSN($orderId, $sn);
                $this->response->setJsonContent(['status' => 'OK', 'data' => $info]);
            } else {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Bad Request']);
            }

            return $this->response;
        }
    }
}
