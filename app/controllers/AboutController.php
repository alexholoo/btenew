<?php
namespace App\Controllers;

/**
 * Display the "About" page.
 */
class AboutController extends ControllerBase
{
    public function indexAction()
    {
        // we can put job into queue, worker will run it later
        $this->queue->put(array( // why is so slow?
            'Test' => __METHOD__ . '-' . rand(),
        ));

        // we can also run the job directly in controller
        // exec('psexec -d php.exe ../job/beanstalk/Test.php');

        #$this->queue->put(array(
        #    'PriceAvailJob' => 0,
        #));
    }
}
