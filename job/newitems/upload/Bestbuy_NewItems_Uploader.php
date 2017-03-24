<?php

class Bestbuy_NewItems extends NewItemsUploader
{
    public function upload()
    {
        $filename = Filenames::get('bestbuy.newitems');
    }
}
