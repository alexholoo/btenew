<?php

namespace Api\Controllers;

use Phalcon\Mvc\Controller;

class QueryController extends ControllerBase
{
    public function initialize()
    {
        $this->view->disable();
    }

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

    public function orderSkuAction($orderId)
    {
        if (strlen($orderId) == 17) { // Amazon Order Number without -
            $orderId = substr($orderId, 0, 3).'-'.substr($orderId, 3, 7).'-'.substr($orderId, 10);
        }

        $items = $this->orderService->getOrderItems($orderId);
        $skus = array_column($items, 'sku');
        $this->response->setJsonContent(['status' => 'OK', 'data' => $skus ]);

        return $this->response;
    }
}
