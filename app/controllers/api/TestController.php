<?php

namespace Api\Controllers;

use Phalcon\Mvc\Controller;

class TestController extends ControllerBase
{
    public function initialize()
    {
        $this->view->disable();
    }

    public function indexAction()
    {
        echo __METHOD__;
    }

    public function testAction()
    {
        echo __METHOD__;
    }
}
