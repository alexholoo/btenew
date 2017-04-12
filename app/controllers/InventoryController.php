<?php

namespace App\Controllers;

class InventoryController extends ControllerBase
{
    public function searchAction()
    {
        $this->view->pageTitle = 'Inventory Location';

        $this->view->data = [];
        $this->view->keyword = '';
        $this->view->searchby = 'partnum';

        if ($this->request->isPost()) {
            $keyword = $this->request->getPost('keyword', 'trim');
            $searchby = $this->request->getPost('searchby');

            $this->view->keyword = $keyword;
            $this->view->searchby = $searchby;

            $data = $this->inventoryService->searchLocation($keyword, $searchby);

            $this->view->data = $data;
        }
    }

    public function addAction()
    {
        $this->view->disable();

        if ($this->request->isPost()) {
            $partnum  = $this->request->getPost('partnum');
            $upc      = $this->request->getPost('upc');
            $location = $this->request->getPost('location');
            $qty      = $this->request->getPost('qty', 'int');

            try {
                $this->inventoryService->addToLocation(
                    compact(
                        'partnum',
                        'upc',
                        'location',
                        'qty'
                    )
                );
            } catch (\Exception $e) {
                $this->response->setJsonContent([
                    'status'  => 'ERROR',
                    'message' => $e->getMessage()
                ]);
                return $this->response;
            }

            $this->response->setJsonContent(['status' => 'OK']);
            return $this->response;
        }
    }
}
