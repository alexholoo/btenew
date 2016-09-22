<?php

namespace App\Controllers;

class JobController extends ControllerBase
{
    public function initialize()
    {
        $this->view->disable();
    }

    public function indexAction()
    {
        echo "No job name specified";
    }

    public function testAction()
    {
        // exec("psexec -d c:/xampp/php/php ../$name.php");
        $this->runJob('job/Test');
        // exec('psexec -d c:/xampp/php/php ../job/Test.php');
    }

    public function orderImportAction()
    {
        $this->runJob('bin/scripts/ca_order_notes');
        $this->runJob('bin/scripts/all_mgn_orders');
    }
}
