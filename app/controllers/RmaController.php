<?php

namespace App\Controllers;

class RmaController extends ControllerBase
{
    public function indexAction()
    {
    }

    public function recordsAction()
    {
        $this->view->pageTitle = 'RMA Records';
        $this->view->records = $this->rmaService->getRecords();
    }
}
