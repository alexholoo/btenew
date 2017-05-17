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

    public function noteAction()
    {
        if ($this->request->isPost()) {
            try {
                $id = $this->request->getPost('id');
                $note = $this->request->getPost('note', 'striptags');

                $this->inventoryService->update($id, ['notes' => $note]);
                $this->response->setJsonContent(['status' => 'OK', 'data' => $note]);
            } catch (\Exception $e) {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => $e->getMessage()]);
            }

            return $this->response;
        }
    }
}
