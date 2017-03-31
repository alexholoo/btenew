<?php

class Bestbuy_NewItems_Uploader extends NewItems_Uploader
{
    public function run($argv = [])
    {
        $this->upload();
    }

    public function upload()
    {
        $filename = Filenames::get('bestbuy.newitems');
    }
}
