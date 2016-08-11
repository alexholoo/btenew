<?php
namespace App\Controllers;

class ShipmentController extends ControllerBase
{
    public function searchAction()
    {
        $this->view->data = [];
        $this->view->keyword = '';
        $this->view->searchby = 'order_id';

        if ($this->request->isPost()) {
            $keyword = $this->request->getPost('keyword');
            $searchby = $this->request->getPost('searchby');

            // ORM doesn't help for the special queries
            if ($searchby == 'order_id') {
                $sql = 'SELECT * FROM master_shipment WHERE order_id LIKE ?';
                $result = $this->db->query($sql, array("%$keyword"));
            } else {
                $sql = "SELECT * FROM master_shipment WHERE MATCH(shipping_address) AGAINST('$keyword' IN NATURAL LANGUAGE MODE)";
                $result = $this->db->query($sql);
            }

            $data = [];
            while ($row = $result->fetch(\Phalcon\Db::FETCH_ASSOC)) {
               $data[] = $row;
            }

            $this->view->keyword = $keyword;
            $this->view->searchby = $searchby;
            $this->view->data = $data;
        }
    }
}
