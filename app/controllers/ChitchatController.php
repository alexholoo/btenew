<?php

namespace App\Controllers;

class ChitchatController extends ControllerBase
{
    public function indexAction()
    {
        $saved = 0;

        if ($this->request->isPost()) {
            if (($info = $this->request->getPost('Tracking'))) {
                $saved = $this->chitchatService->save($info);
            }
            else if ($this->request->getPost('Delete')) {
                $items = $this->request->getPost('Items');
                $this->chitchatService->delete($items);
            }
            else if ($this->request->getPost('Delete_All')) {
                $this->chitchatService->deleteAll();
            }
            else if ($this->request->getPost('Export')) {
                $this->chitchatService->export();
            }
        }

        $this->view->saved = $saved;
        $this->view->list  = $this->chitchatService->list();
    }
}
