<?php

namespace App\Controllers;

class AboutController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->data = gethostname();
    }
}
