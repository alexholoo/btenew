<?php

namespace App\Controllers;

class SearchController extends ControllerBase
{
    public function indexAction()
    {
    }

    public function priceAvailAction()
    {
        if ($this->request->isGet()) {
            $sku = $this->request->getQuery('sku');
        }

        if ($this->request->isPost()) {
            $sku = $this->request->getPost('sku');
        }

        if ($sku) {
            try {
                $skus = $this->skuService->getAltSkus($sku);

                // Make a xmlapi call to get price and availability
                $data = $this->priceAvailService->getPriceAvailability($skus);
                $this->view->data = $data;
            } catch (\Exception $e) {
                $this->view->error = $e->getMessage();
            }
        }

        $this->view->sku = $sku;
    }

    public function skuAction()
    {
        if ($this->request->isGet()) {
            $sku = $this->request->getQuery('sku');
        }

        if ($this->request->isPost()) {
            $sku = $this->request->getPost('sku');
        }

        if ($sku) {
            $info = $this->skuService->getMasterSku($sku);
            $this->view->data = $info;
        }

        $this->view->sku = $sku;
    }
}
