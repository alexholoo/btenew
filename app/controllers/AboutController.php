<?php

namespace App\Controllers;

class AboutController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->pageTitle = 'About';
        $this->view->host = gethostname();
        $this->view->serverIP = $this->request->getServerAddress();
        $this->view->clientIP = $this->request->getClientAddress();
        $this->view->userAgent = $this->request->getUserAgent();
    }

    public function testAction()
    {
        $this->view->pageTitle = 'Test';
    }

    public function infoAction()
    {
    }
}
