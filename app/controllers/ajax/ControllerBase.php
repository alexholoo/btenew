<?php

namespace Ajax\Controllers;

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public function initialize()
    {
        // TODO: restrict /ajax/* to AJAX request only
        $this->view->disable();
    }
}
