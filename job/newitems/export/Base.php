<?php

abstract class NewItemsExporter extends Job
{
    abstract public function export();

    protected function loadMasterSkuList()
    {
        static $skuList = [];

        if ($skuList) {
            return $skuList;
        }

        $sql = "SELECT * FROM master_sku_list";
        $result = $this->db->fetchAll($sql);

        $skuList = array_column($result, null, 'SKU');

        return $skuList;
    }
}
