<?php

class Amazon_PriceQty_Uploader extends PriceQty_Uploader
{
    public function run($argv = [])
    {
        $this->upload();
    }

    public function upload()
    {
        // @see AmazonPriceQtyUpdateJob.php
    }
}
