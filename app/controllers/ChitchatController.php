<?php

namespace App\Controllers;

class ChitchatController extends ControllerBase
{
    public function initialize()
    {
        $this->view->disable();
    }

    // chitchat/list
    public function listAction()
    {
        $info = $this->chitchatService->list();

        if ($info) {
            $this->response->setJsonContent(['status' => 'OK', 'data' => $info]);
        } else {
            $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Tracking not found']);
        }

        return $this->response;
    }

    // chitchat/save?KEY=VAL&...
    public function saveAction()
    {
        $info = $this->request->getQuery();

        $this->chitchatService->save($info);

        $this->response->setJsonContent(['status' => 'OK', 'data' => array() ]);

        return $this->response;
    }

    // chitchat/export
    public function exportAction()
    {
        $this->chitchatService->export();
    }

    // chitchat/delete?id=TRACKING_NUMBER
    public function deleteAction()
    {
        $id = $this->request->getQuery('id');

        $this->chitchatService->delete($id);

        $this->response->setJsonContent(['status' => 'OK', 'data' => array() ]);

        return $this->response;
    }

    // chitchat/clear
    public function clearAction()
    {
        $this->chitchatService->clear();

        $this->response->setJsonContent(['status' => 'OK', 'data' => array() ]);

        return $this->response;
    }
}
