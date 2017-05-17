<?php

namespace Ajax\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;

class ControllerBase extends Controller
{
    public function initialize()
    {
        $this->view->disable();
    }

    // TODO: remove __ to restrict /ajax/* to AJAX request only
    public function __beforeExecuteRoute(Dispatcher $dispatcher)
    {
        if (!$this->request->isAjax()) {
            return false;
        }

        return true;
    }
}
