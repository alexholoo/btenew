<?php

namespace App\Controllers;

class SearchController extends ControllerBase
{
    public function indexAction()
    {
    }

    public function priceAvailAction()
    {
        $sku = '';

        if ($this->request->isPost()) {
            $sku = $this->request->getPost('sku');

            try {
                $skus = $this->productService->getSkuGroup($sku);
                fpr($skus);
                // Make a xmlapi call to get price and availability
                $data = $this->priceAvailService->getPriceAvailability($skus);
                $this->view->data = $data;
                fpr($data);
            } catch (\Exception $e) {
                $this->view->error = $e->getMessage();
            }
        }

        $this->view->sku = $sku;
    }
}
