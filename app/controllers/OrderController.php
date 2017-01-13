<?php

namespace App\Controllers;

class OrderController extends ControllerBase
{
    public function getAction()
    {
        $this->dispatcher->forward([
            'controller' => 'query',
            'action'     => 'order',
        ]);
    }
}
