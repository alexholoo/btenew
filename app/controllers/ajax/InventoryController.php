<?php

namespace Ajax\Controllers;

use App\Models\Orders;

class InventoryController extends ControllerBase
{
    public function addAction()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getPost();

            try {
                $this->inventoryService->add($data);
                $this->response->setJsonContent(['status' => 'OK', 'data' => '']);
            } catch (\Exception $e) {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => $e->getMessage()]);
            }

            return $this->response;
        }
    }
}

