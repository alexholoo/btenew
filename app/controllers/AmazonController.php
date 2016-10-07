<?php

namespace App\Controllers;

class AmazonController extends ControllerBase
{
    public function indexAction()
    {
    }

    public function fbaItemsAction()
    {
        $this->view->retry = false;
        $this->view->upc = '';
        $this->view->mpn = '';
        $this->view->cost = '';
        $this->view->us_floor = '';
        $this->view->ca_floor = '';
        $this->view->condition = '';

        if ($this->request->isGet()) {
            $this->session->set('fbaitems', []);
        }

        if ($this->request->isPost()) {
            $retry = $this->request->getPost('retry');
            $upc = $this->request->getPost('upc');
            $mpn = $this->request->getPost('mpn');
            $cost = $this->request->getPost('cost');
            $floorCA = $this->request->getPost('ca_floor');
            $floorUS = $this->request->getPost('us_floor');
            $condition = $this->request->getPost('condition');

            $sql = 'SELECT * FROM master_sku_list';

            $where = [];

            if ($upc) {
                $where[] = "upc='$upc'";
            }

            if ($mpn && !$retry) {
                $where[] = "mpn='$mpn'";
            }

            if (empty($where)) {
                return;
            }

            if ($where) {
                $sql .= ' WHERE ' . implode(' AND ', $where);
            }

            $item = $this->db->fetchOne($sql);

            if ($item) {
                $data = $this->session->get('fbaitems');

                // if MPN is empty, force user to input one
                if (empty($item['MPN']) && empty($mpn)) {
                    $this->view->retry = true;
                    $this->view->upc = $upc;
                    $this->view->mpn = $mpn;
                    $this->view->cost = $cost;
                    $this->view->us_floor = $floorCA;
                    $this->view->ca_floor = $floorUS;
                    $this->view->condition = $condition;
                    return;
                }

                // if there is no $cost input, use default cost
                if (!$cost) {
                    $cost = $item['best_cost'];
                }

                $data[] = [
                    'partnum'    => 'BTE-FBA-'.$mpn,
                    'notes'      => 'FBA',
                    'us_floor'   => $floorUS,
                    'ca_floor'   => $floorCA,
                    'title'      => $item['name'],
                    'cost'       => $cost,
                    'condition'  => $condition,
                    'mpn'        => $item['MPN'],
                    'source'     => $item['source'],
                    'source_sku' => $item['SKU'],
                    'id'         => $item['id'],
                    'upc'        => $item['UPC'],
                ];

                $this->session->set('fbaitems', $data);
                $this->view->items = $data;
            }
        }
    }
}
