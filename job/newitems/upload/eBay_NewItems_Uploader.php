<?php

class eBay_NewItems_Uploader extends NewItems_Uploader
{
    public function run($argv = [])
    {
        $this->upload();
    }

    public function upload()
    {
        $filename = Filenames::get('ebay.gfs.newitems');
        $filename = Filenames::get('ebay.odo.newitems');
    }
}
