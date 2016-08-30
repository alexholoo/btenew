<?php

namespace Service;

use Phalcon\Di\Injectable;

class EbayService extends Injectable
{
    public function doSomething()
    {
        fpr(__FILE__."\n".__METHOD__);

        $logger = $this->di->get('logger');
        $logger->info('Hello from ' . __METHOD__);
    }
}
