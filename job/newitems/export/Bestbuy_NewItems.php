<?php

class Bestbuy_NewItems extends NewItemsExporter
{
    public function export()
    {
        $filename = Filenames::get('bestbuy.newitems');
    }
}
