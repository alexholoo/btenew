<?php

class Newegg_NewItems extends NewItemsExporter
{
    public function export()
    {
        $filename = Filenames::get('newegg.ca.newitems');
    }
}
