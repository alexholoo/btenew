<?php

namespace Api\Controllers;

use Phalcon\Mvc\Controller;

class InventoryController extends ControllerBase
{
    /**
     * Add a new item to inventory, for mobile use only
     *
     * /api/inventory/new
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
