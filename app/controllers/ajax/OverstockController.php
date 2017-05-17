<?php

namespace Ajax\Controllers;

use App\Models\Orders;

class OverstockController extends ControllerBase
{
    public function noteAction()
    {
        if ($this->request->isPost()) {
            try {
                $id = $this->request->getPost('id');
                $note = $this->request->getPost('note', 'striptags');

                $this->overstockService->update($id, ['note' => $note]);
                $this->response->setJsonContent(['status' => 'OK', 'data' => $note ]);
            } catch (\Exception $e) {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => $e->getMessage()]);
            }

            return $this->response;
        }
    }
}
