<?php
namespace App\Controllers;

class PurchaseController extends ControllerBase
{
    public function assistAction()
    {
        $sql = 'SELECT * FROM ca_order_notes';
        $result = $this->db->query($sql);

        $data = [];
        while ($row = $result->fetch(\Phalcon\Db::FETCH_ASSOC)) {
            $row['related_sku'] = explode('|', $row['related_sku']);
            $row['related_sku'] = array_map('trim', $row['related_sku']);
            $row['related_sku'] = array_filter($row['related_sku']);

            $data[] = $row;
        }

        $this->view->data = $data;
    }
}
