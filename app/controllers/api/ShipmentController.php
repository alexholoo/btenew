<?php

namespace Api\Controllers;

use Phalcon\Mvc\Controller;

class ShipmentController extends ControllerBase
{
    public function initialize()
    {
        $this->view->disable();
    }

    public function newAction($trackingNum = '')
    {
        $info = $this->shipmentService->addShipment($trackingNum);
        $this->response->setJsonContent(['status' => 'OK', 'data' => $info]);

        return $this->response;
    }
}
