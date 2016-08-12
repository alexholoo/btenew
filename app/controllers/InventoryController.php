<?php
namespace App\Controllers;

class InventoryController extends ControllerBase
{
    public function searchAction()
    {
        $this->view->data = [];
        $this->view->keyword = '';
        $this->view->searchby = 'partnum';

        if ($this->request->isPost()) {
            $keyword = $this->request->getPost('keyword', 'striptags');
            $searchby = $this->request->getPost('searchby');

            $this->view->keyword = $keyword;
            $this->view->searchby = $searchby;

            // ORM doesn't help for the special queries
            if ($searchby == 'partnum') {
                $sql = 'SELECT * FROM inventory WHERE partnum LIKE ?';
                $result = $this->db->query($sql, array("%$keyword%"));
            } elseif ($searchby == 'upc') {
                $sql = 'SELECT * FROM inventory WHERE upc = ?';
                $result = $this->db->query($sql, array($keyword));
            } elseif ($searchby == 'location') {
                $sql = "SELECT * FROM inventory WHERE location = ?";
                $result = $this->db->query($sql, array($keyword));
            } elseif ($searchby == 'qty') {
                if (!ctype_digit($keyword)) {
                    return;
                }
                $sql = "SELECT * FROM inventory WHERE qty >= ?";
                $result = $this->db->query($sql, array($keyword));
            } else {
                return;
            }

            $data = [];
            while ($row = $result->fetch(\Phalcon\Db::FETCH_ASSOC)) {
               $data[] = $row;
            }

            $this->view->data = $data;
        }
    }
}
