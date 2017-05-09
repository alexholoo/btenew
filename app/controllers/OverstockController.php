<?php

namespace App\Controllers;

class OverstockController extends ControllerBase
{
    const SESSKEY = 'overstock-newitems';

    public function indexAction()
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
                    'allocation' => 'All',
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
            $filename = "E:/BTE/import/overstock-add.csv";

            $fp = fopen($filename, 'w');
            fputcsv($fp, array_keys($data[0]));

            foreach ($data as $row) {
                fputcsv($fp, $row);
            }

            fclose($fp);

            $this->runJob('job/OverstockAddJob');
        }

        $this->session->set(self::SESSKEY, []);

        $this->response->redirect('overstock/index');
    }
}