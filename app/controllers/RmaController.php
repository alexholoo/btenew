<?php

namespace App\Controllers;

use Phalcon\Paginator\Adapter\NativeArray as Paginator;

class RmaController extends ControllerBase
{
    public function indexAction()
    {
    }

    public function recordsAction()
    {
        $this->view->pageTitle = 'RMA Records';

        $currentPage = $this->request->getQuery('page', 'int', 1);
        $records = $this->rmaService->getRecords();

        $paginator = new Paginator([
            "data"  => $records,
            "limit" => 20,
            "page"  => $currentPage,
        ]);

        $this->view->page = $paginator->getPaginate();
    }
}
