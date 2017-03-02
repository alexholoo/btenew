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

    // job/run?name=JOBNAME
    public function runAction()
    {
        $name = $this->request->getQuery('name');
        $this->runJob("job/$name");
    }

    // job/test
    public function testAction()
    {
        // exec("psexec -d c:/xampp/php/php ../$name.php");
        $this->runJob('job/Test');
        // exec('psexec -d c:/xampp/php/php ../job/Test.php');
    }

    // job/orderimport
    public function orderImportAction()
    {
        $this->runJob('bin/scripts/ca_order_notes');
        $this->runJob('bin/scripts/all_mgn_orders');
    }

    // job/amazonupdate
    public function amazonUpdateAction()
    {
       #$this->runJob('job/AmazonNewItemsJob');
        $this->runJob('job/AmazonPriceQtyUpdateJob');
       #$this->runJob('job/AmazonShippingTemplateJob');

        echo "Jobs are running, please check on Amazon Seller Central a minute later.";
    }

    // job/importshippingeasy
    public function importShippingEasyAction()
    {
        $this->runJob('job/ShippingEasyImportJob');

        echo "ShippingEasy imported, now you can start order scanning.";
    }
}
