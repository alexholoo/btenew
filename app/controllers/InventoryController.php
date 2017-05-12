<?php

namespace App\Controllers;

use Phalcon\Paginator\Adapter\NativeArray as Paginator;

class InventoryController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->pageTitle = 'BTE Inventory';

        if ($this->request->isPost()) {
            $keyword = $this->request->getPost('keyword', 'trim');
            $data = $this->inventoryService->search($keyword);
            $currentPage = 1;
        } else {
            $currentPage = $this->request->getQuery('page', 'int', 1);
            $data = $this->inventoryService->load();
        }

        array_walk($data, function(&$item, $key) {
            $item['updatedon'] = substr($item['updatedon'], 0, 10);
        });

        $paginator = new Paginator([
            "data"  => $data,
            "limit" => 20,
            "page"  => $currentPage,
        ]);

        $this->view->today = date('Y-m-d');
        $this->view->page = $paginator->getPaginate();
    }

    public function viewChangeAction()
    {
        $this->view->pageTitle = 'Inventory Deduction';

        $currentPage = $this->request->getQuery('page', 'int', 1);
        $data = $this->inventoryService->loadChanges();

        $paginator = new Paginator([
            "data"  => $data,
            "limit" => 20,
            "page"  => $currentPage,
        ]);

        $this->view->today = date('Y-m-d');
        $this->view->page = $paginator->getPaginate();
    }
}
