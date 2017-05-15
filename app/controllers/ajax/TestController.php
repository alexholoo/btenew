<?php

namespace Ajax\Controllers;

use Phalcon\Mvc\Controller;

class TestController extends ControllerBase
{
    public function indexAction()
    {
        echo __METHOD__;
    }

    public function testAction()
    {
        echo __METHOD__;
    }
}
