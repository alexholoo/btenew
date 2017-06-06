<?php

namespace Ajax\Controllers;

use App\Models\Orders;

class OverstockController extends ControllerBase
{
    const SESSKEY = 'overstock-newitems';

    /**
     * from page /overstock/add
     */
    public function updateAction()
    {
        if ($this->request->isPost()) {

            $pk = $this->request->getPost('pk');
            $name = $this->request->getPost('name');
            $value = $this->request->getPost('value');

            $data = $this->session->get(self::SESSKEY);

            if (isset($data[$pk])) {
                $data[$pk][$name] = $value;
                $this->session->set(self::SESSKEY, $data);
            }

            $this->response->setJsonContent(['status' => 'OK']);
            return $this->response;
        }
    }

    /**
     * from page /overstock/add
     */
    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $index = $this->request->getPost('index');

            $data = $this->session->get(self::SESSKEY);
            array_splice($data, $index, 1);
            $this->session->set(self::SESSKEY, $data);

            $this->response->setJsonContent(['status' => 'OK']);
            return $this->response;
        }
    }

    /**
     * from page /overstock
     */
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

    /**
     * from page /overstock
     */
    public function getAction($id = 0)
    {
        try {
            $info = $this->overstockService->getById($id);
            $this->response->setJsonContent(['status' => 'OK', 'data' => $info ]);
        } catch (\Exception $e) {
            $this->response->setJsonContent(['status' => 'ERROR', 'message' => $e->getMessage()]);
        }

        return $this->response;
    }
}
