<?php

class Rakuten_NewItems extends NewItemsExporter
{
    public function export()
    {
        $filename = Filenames::get('rakuten.us.newitems');
    }
}
