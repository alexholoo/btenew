<?php

namespace App\Controllers;

use Phalcon\Paginator\Adapter\NativeArray as Paginator;

class RmaController extends ControllerBase
{
    public function indexAction()
    {
        $this->dispatcher->forward([
            'controller' => 'rma',
            'action'     => 'records',
        ]);
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

    public function detailAction($id)
    {
        $this->view->pageTitle = 'RMA Detail';
        $this->view->detail = $this->rmaService->getDetail($id);
    }

    public function updateAction()
    {
        $this->view->disable();
        $this->response->setJsonContent(['status' => 'OK', 'data' => '']);
    }

    public function addAction()
    {
        $this->view->pageTitle = 'New RMA Record';
    }
}
