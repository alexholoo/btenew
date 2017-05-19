<?php

namespace App\Controllers;

class QueryController extends ControllerBase
{
    /**
     * http://192.168.0.116/dataexchange/chitchat.php needs this
     *
     * /query/shippingeasy?id=ORDER_NUMBER|TRACKING_NUMBER
     */
    public function shippingEasyAction()
    {
        // id can be OrderNumber OR TrackingNumber
        $id = $this->request->getQuery('id');

        $info = $this->shipmentService->getShippingEasy($id);

        if ($info) {
            $this->response->setJsonContent(['status' => 'OK', 'data' => $info]);
        } else {
            $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Tracking not found']);
        }

        return $this->response;
    }
}
