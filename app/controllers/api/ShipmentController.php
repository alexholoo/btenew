<?php

namespace Api\Controllers;

use Phalcon\Mvc\Controller;

class ShipmentController extends ControllerBase
{
    public function initialize()
    {
        $this->view->disable();
    }

    public function newAction()
    {
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
}
