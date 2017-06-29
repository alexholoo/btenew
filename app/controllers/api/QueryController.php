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

    public function trackingSkuAction($trackingNum)
    {
        $order = $this->shipmentService->getOrderByTracking($trackingNum);
        if ($order) {
            $orderId = $order['order_id'];
            $items = $this->orderService->getOrderItems($orderId);
            $skus = array_column($items, 'sku');
            $data = [ 'orderNo' => $orderId, 'items' => $skus ];
            $this->response->setJsonContent(['status' => 'OK', 'data' => $data ]);
        } else {
            $this->response->setJsonContent(['status' => 'OK', 'data' => [] ]);
        }

        return $this->response;
    }

    public function trackingSkuLocAction($trackingNum)
    {
        $order = $this->shipmentService->getOrderByTracking($trackingNum);
        if ($order) {
            $orderId = $order['order_id'];
            $items = $this->orderService->getOrderItems($orderId);
            $skus = array_column($items, 'sku');

            $locations = [];
            foreach ($skus as $sku) {
                $upc = $this->skuService->getUpc($sku);
                $loc = $this->inventoryLocationService->findUpc($upc);
                $locations[] = [
                    'sku' => $sku,
                    'loc' => array_column($loc, 'location')
                ];
            }

            $data = [ 'orderNo' => $orderId, 'items' => $locations ];
            $this->response->setJsonContent(['status' => 'OK', 'data' => $data ]);
        } else {
            $this->response->setJsonContent(['status' => 'OK', 'data' => [] ]);
        }

        return $this->response;
    }
}
