<?php

abstract class Pricelist_Downloader extends Job
{
    abstract public function download();
}
