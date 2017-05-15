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

    public function noteAction()
    {
        $this->view->disable();

        if ($this->request->isPost()) {
            $id = $this->request->getPost('id');
            $note = $this->request->getPost('note', 'striptags');
            $this->overstockService->update($id, ['note' => $note]);
            $this->response->setJsonContent(['status' => 'OK', 'data' => $note ]);
            return $this->response;
        }
    }

    public function viewLogAction()
    {
        $this->view->pageTitle = 'Overstock Log';

        $currentPage = $this->request->getQuery('page', 'int', 1);
        $data = $this->overstockService->loadLogs();

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

    public function viewChangeAction()
    {
        $this->view->pageTitle = 'Overstock Deduction';

        $currentPage = $this->request->getQuery('page', 'int', 1);
        $data = $this->overstockService->loadChanges();

        $paginator = new Paginator([
            "data"  => $data,
            "limit" => 20,
            "page"  => $currentPage,
        ]);

        $this->view->today = date('Y-m-d');
        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Ajax Handler
     */
    public function deleteAction()
    {
        $this->view->disable();

        if ($this->request->isPost()) {
            $index = $this->request->getPost('index');

            $data = $this->session->get(self::SESSKEY);
            array_splice($data, $index, 1);
            $this->session->set(self::SESSKEY, $data);

            $this->response->setJsonContent(['status' => 'OK']);
            return $this->response;
        }
    }

    /**
     * Ajax Handler
     */
    public function updateAction()
    {
        $this->view->disable();

        if ($this->request->isPost()) {

            $pk = $this->request->getPost('pk');
            $name = $this->request->getPost('name');
            $value = $this->request->getPost('value');

            $data = $this->session->get(self::SESSKEY);

            if (isset($data[$pk])) {
                $data[$pk][$name] = $value;
                $this->session->set(self::SESSKEY, $data);
            }

            $this->response->setJsonContent(['status' => 'OK']);
            return $this->response;
        }
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

    /**
     * Ajax Handler (for mobile use)
     */
    public function newAction()
    {
        $this->view->disable();

        if ($this->request->isPost()) {
            $partnum = $this->request->getPost('partnum');
            $upc = $this->request->getPost('upc');
            $qty = $this->request->getPost('qty');
            $location = $this->request->getPost('location');

            if ($this->overstockService->newStock($partnum, $upc, $qty, $location)) {
                $this->response->setJsonContent(['status' => 'OK']);
            } else {
                $this->response->setJsonContent(['status' => 'ERROR', 'message' => 'Item not found']);
            }

            return $this->response;
        }
    }
}
