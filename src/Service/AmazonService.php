<?php

namespace Service;

use Phalcon\Di\Injectable;

class AmazonService extends Injectable
{
    public function doSomething()
    {
        fpr(__FILE__."\n".__METHOD__);

        $ebayService = $this->di->get('ebayService');
        $ebayService->doSomething();
    }
}
