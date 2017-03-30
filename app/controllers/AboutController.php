<?php

namespace App\Controllers;

class AboutController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->pageTitle = 'About';
        $this->view->data = gethostname();
    }
}
