<?php

namespace Service;

use Phalcon\Di\Injectable;

class ChitchatService extends Injectable
{
    public function list()
    {
        $sql = "SELECT * FROM chitchat";
        $info = $this->db->fetchAll($sql);
        return $info;
    }

    public function save($data)
    {
    }

    public function export()
    {
    }

    public function delete($id)
    {
    }

    public function clear()
    {
        $this->db->execute("TRUNCATE TABLE chitchat");
    }
}
