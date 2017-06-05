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
        $trackingNum = $this->request->getQuery('trackingnum');
        $user = $this->request->getQuery('user');

        if ($trackingNum && $user) {
            $info = $this->shipmentService->addShipment($trackingNum, $user);
            $this->response->setJsonContent(['status' => 'OK', 'data' => $info]);
        } else {
            $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Bad Request']);
        }

        return $this->response;
    }
}
