<?php

namespace Api\Controllers;

class InventoryController extends ControllerBase
{
    public function initialize()
    {
        $this->view->disable();
    }

    public function locAction()
    {
        if ($this->request->isPost()) {

            $data = $this->request->getPost();

            $data['partnum'] = $data['mpn'];
            $data['qty']     = $data['quantity'];
            $data['note']    = strip_tags($data['note']);
            $data['sn']      = strip_tags($data['sn']);

            unset($data['name']);
            unset($data['mpn']);
            unset($data['quantity']);
            unset($data['id']);

            $id = $this->request->getPost('id');

            if ($id) {
                $this->inventoryLocationService->update($id, $data);
            } else {
                $this->inventoryLocationService->add($data);
            }

            $this->response->setJsonContent(['status' => 'OK']);

            return $this->response;
        }
    }

    public function invoiceAction()
    {
        if ($this->request->isPost()) {

            $data = $this->request->getPost();

            $this->inventoryLocationService->saveInvoice($data);

            $this->response->setJsonContent(['status' => 'OK']);

            return $this->response;
        }
    }
}
