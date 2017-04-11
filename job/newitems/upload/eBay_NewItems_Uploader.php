<?php

class eBay_NewItems_Uploader extends NewItems_Uploader
{
    public function run($argv = [])
    {
        try {
            $this->upload();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function upload()
    {
        $filename = Filenames::get('ebay.gfs.newitems');
        $filename = Filenames::get('ebay.odo.newitems');
    }
}
