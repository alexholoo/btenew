<?php

class eBay_NewItems extends NewItemsUploader
{
    public function upload()
    {
        $filename = Filenames::get('ebay.bte.newitems');
        $filename = Filenames::get('ebay.odo.newitems');
    }
}
