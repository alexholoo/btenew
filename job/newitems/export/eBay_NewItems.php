<?php

class eBay_NewItems extends NewItemsExporter
{
    public function export()
    {
        $filename = Filenames::get('ebay.bte.newitems');
        $filename = Filenames::get('ebay.odo.newitems');
    }
}
