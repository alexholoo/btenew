<?php

namespace Ajax\Controllers;

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public function initialize()
    {
        $this->view->disable();
    }
}
