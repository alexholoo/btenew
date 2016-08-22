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
            $keyword = $this->request->getPost('keyword', 'trim');
            $searchby = $this->request->getPost('searchby');

            $this->view->keyword = $keyword;
            $this->view->searchby = $searchby;

            // ORM doesn't help for the special queries
            if ($searchby == 'partnum') {
                $sql = 'SELECT * FROM inventory_location WHERE partnum LIKE ?';
                $result = $this->db->query($sql, array("%$keyword%"));
            } elseif ($searchby == 'upc') {
                $sql = 'SELECT * FROM inventory_location WHERE upc LIKE ?';
                $result = $this->db->query($sql, array("%$keyword"));
            } elseif ($searchby == 'location') {
                $sql = "SELECT * FROM inventory_location WHERE location = ?";
                $result = $this->db->query($sql, array($keyword));
            } elseif ($searchby == 'qty') {
                if (!ctype_digit($keyword)) {
                    return;
                }
                $sql = "SELECT * FROM inventory_location WHERE qty >= ?";
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

    public function addAction()
    {
        $this->view->disable();

        // TODO: who is doing this? add a new column(userid) to table.
        if ($this->request->isPost()) {
            $partnum  = $this->request->getPost('partnum');
            $upc      = $this->request->getPost('upc');
            $location = $this->request->getPost('location');
            $qty      = $this->request->getPost('qty', 'int');

            try {
                $success = $this->db->insertAsDict('inventory_location',
                    array(
                        'partnum'  => $partnum,
                        'upc'      => $upc,
                        'location' => $location,
                        'qty'      => $qty,
                        'sn'       => '',
                        'note'     => '',
                    )
                );
            } catch (Exception $e) {
                $this->response->setJsonContent([
                    'status'  => 'ERROR',
                    'message' => $e->getMessage()
                ]);
                return $this->response;
            }

            $this->response->setJsonContent(['status' => 'OK']);
            return $this->response;
        }
    }
}
