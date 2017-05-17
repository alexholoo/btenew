<?php

namespace App\Controllers;

use Phalcon\Paginator\Adapter\NativeArray as Paginator;

class OverstockController extends ControllerBase
{
    const SESSKEY = 'overstock-newitems';

    public function indexAction()
    {
        $this->view->pageTitle = 'Overstock';

        if ($this->request->isPost()) {
            $keyword = $this->request->getPost('keyword', 'trim');
            $data = $this->overstockService->search($keyword);
            $currentPage = 1;
        } else {
            $currentPage = $this->request->getQuery('page', 'int', 1);
            $data = $this->overstockService->load();
        }

        array_walk($data, function(&$item, $key) {
            $item['updatedon'] = substr($item['updatedon'], 0, 10);
        });

        $paginator = new Paginator([
            "data"  => $data,
            "limit" => 20,
            "page"  => $currentPage,
        ]);

        $this->view->today = date('Y-m-d');
        $this->view->page = $paginator->getPaginate();
    }

    public function addAction()
    {
        $this->view->pageTitle = 'New Overstock Items';
        $this->view->searchby = 'sku';

        if ($this->request->isGet()) {
            //$this->session->set(self::SESSKEY, []);
        }

        $data = $this->session->get(self::SESSKEY);

        if ($this->request->isPost()) {

            $keyword = $this->request->getPost('keyword');
            $info = $this->skuService->getMasterSku($keyword);

            if ($info) {
                $sku = $info['recommended_pn'];
                $mfr = $info['MFR'];
                $altSkus = $this->skuService->getAltSkus($sku);
                $data[] = [
                    'sku'        => $sku,
                    'title'      => $mfr. ' ' .$info['name'],
                    'cost'       => round($info['best_cost']),
                    'condition'  => 'New',
                    'allocation' => 'ALL',
                    'qty'        => '1',
                    'mpn'        => $info['MPN'],
                    'note'       => implode('; ', $altSkus),
                    'weight'     => $info['Weight'], //round($info['Weight']) ?? '',
                    'upc'        => $info['UPC'],
                ];

                $this->session->set(self::SESSKEY, $data);
            }
        }

        $this->view->items = $data;
    }

    public function viewLogAction()
    {
        $this->view->pageTitle = 'Overstock Log';

        if ($this->request->isPost()) {
            $keyword = $this->request->getPost('keyword', 'trim');
            $data = $this->overstockService->searchLogs($keyword);
            $currentPage = 1;
        } else {
            $currentPage = $this->request->getQuery('page', 'int', 1);
            $data = $this->overstockService->loadLogs();
        }

        array_walk($data, function(&$item, $key) {
            $item['date'] = substr($item['datetime'], 0, 10);
        });

        $paginator = new Paginator([
            "data"  => $data,
            "limit" => 20,
            "page"  => $currentPage,
        ]);

        $this->view->today = date('Y-m-d');
        $this->view->page = $paginator->getPaginate();
    }

    public function viewChangeAction($id = '')
    {
        $this->view->pageTitle = 'Overstock Deduction';

        if ($this->request->isPost()) {
            $keyword = $this->request->getPost('keyword', 'trim');
            $data = $this->overstockService->searchChanges($keyword);
            $currentPage = 1;
        } else {
            $currentPage = $this->request->getQuery('page', 'int', 1);
            $data = $this->overstockService->loadChanges($id);
        }

        $paginator = new Paginator([
            "data"  => $data,
            "limit" => 20,
            "page"  => $currentPage,
        ]);

        $this->view->input = true;
        if (strlen($id) > 0) {
            $this->view->input = false;
        }

        $this->view->today = date('Y-m-d');
        $this->view->page = $paginator->getPaginate();
    }

    public function appendAction()
    {
        $data = $this->session->get(self::SESSKEY);

        if ($data) {
            foreach ($data as $row) {
                $this->overstockService->add($row);
            }
        }

        $this->session->set(self::SESSKEY, []);

        $this->response->redirect('/overstock');
    }
}
