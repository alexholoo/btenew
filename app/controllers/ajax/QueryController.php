<?php

namespace Ajax\Controllers;

use Phalcon\Mvc\Controller;

class QueryController extends ControllerBase
{
    public function upcAction($upc)
    {
        $skuList = $this->skuService->getSkuListByUPC($upc);
        $this->response->setJsonContent(['status' => 'OK', 'data' => $skuList ]);

        return $this->response;
    }

    public function mpnAction($mpn)
    {
        $skuList = $this->skuService->getSkuListByMPN($mpn);
        $this->response->setJsonContent(['status' => 'OK', 'data' => $skuList ]);

        return $this->response;
    }
}
