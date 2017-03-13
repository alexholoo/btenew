<?php

class Amazon_NewItems extends NewItemsExporter
{
    public function export()
    {
        $this->exportNewItemsCA();
        $this->exportNewItemsUS();
    }

    protected function exportNewItemsCA()
    {
        $store = 'bte-amazon-ca';
        $filename = Filenames::get('amazon.ca.newitems');
    }

    protected function exportNewItemsUS()
    {
        $store = 'bte-amazon-us';
        $filename = Filenames::get('amazon.us.newitems');
    }
}
