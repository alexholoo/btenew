<?php

namespace App\Controllers;

class SearchController extends ControllerBase
{
    public function indexAction()
    {
    }

    public function priceAvailAction()
    {
        $this->view->pageTitle = 'Price Availability';

        if ($this->request->isGet()) {
            $sku = $this->request->getQuery('sku', 'trim');
        }

        if ($this->request->isPost()) {
            $sku = $this->request->getPost('sku', 'trim');
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
        $this->view->pageTitle = 'SKU Information';

        if ($this->request->isGet()) {
            $sku = $this->request->getQuery('sku', 'trim');
        }

        if ($this->request->isPost()) {
            $sku = $this->request->getPost('sku', 'trim');
        }

        if ($sku) {
            $info = $this->skuService->getMasterSku($sku);
            $this->view->data = $info;
            $this->view->desc = $this->skuService->getDescription($sku);
            $this->view->imgurl = $this->skuService->getImageUrl($sku);
        }

        $this->view->sku = $sku;
    }
}
