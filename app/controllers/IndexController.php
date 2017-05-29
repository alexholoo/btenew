<?php
namespace App\Controllers;

/**
 * Display the default index page.
 */
class IndexController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->setVar('logged_in', is_array($this->session->get('auth')));
    }
}
