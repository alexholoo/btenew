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

            $data = $this->inventoryLocationService->search($keyword, $searchby);

            $this->view->data = $data;
        }
    }

    public function addAction()
    {
        $sesskey = 'inventory-locations';

       #$this->view->disable();

        $this->view->pageTitle = 'Inventory Location Add';

        $this->view->partnum = '';
        $this->view->upc = '';
        $this->view->location = '';
        $this->view->qty = '';
        $this->view->sn = '';
        $this->view->note = '';

        if ($this->request->isGet()) {
           #$this->session->set($sesskey, []);
        }

        $items = $this->session->get($sesskey);

        if ($this->request->isPost()) {
            $partnum  = $this->request->getPost('partnum');
            $upc      = $this->request->getPost('upc');
            $location = $this->request->getPost('location');
            $qty      = $this->request->getPost('qty', 'int');
            $sn       = $this->request->getPost('sn');
            $note     = $this->request->getPost('note');

            $id = $this->inventoryLocationService->add(
                compact(
                    'partnum',
                    'upc',
                    'location',
                    'qty',
                    'sn',
                    'note'
                )
            );

            $items[] = $this->inventoryLocationService->get($id);

            $this->session->set($sesskey, $items);
        }

        $this->view->items = $items;
    }
}
