<?php

class eBay_NewItems_Uploader extends NewItems_Uploader
{
    public function upload()
    {
        $filename = Filenames::get('ebay.bte.newitems');
        $filename = Filenames::get('ebay.odo.newitems');
    }
}
