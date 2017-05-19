<?php

namespace Api\Controllers;

class OverstockController extends ControllerBase
{
    /**
     * Add a new item to overstock, for mobile use only
     *
     * /api/overstock/new
     */
    public function newAction()
    {
        if ($this->request->isPost()) {
            $partnum = $this->request->getPost('partnum');
            $upc = $this->request->getPost('upc');
            $qty = $this->request->getPost('qty');
            $location = $this->request->getPost('location');

            if ($this->overstockService->newStock($partnum, $upc, $qty, $location)) {
                $this->response->setJsonContent(['status' => 'OK']);
            } else {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Item not found']);
            }

            return $this->response;
        }
    }
}
